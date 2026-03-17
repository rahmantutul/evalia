<?php

namespace App\Services;

use App\Models\KnowledgeBase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class KnowledgeService
{

    private const ARABIC_PREFIXES = [
        'وبال', 'فبال', 'لبال', 'كبال',          // 4-char
        'وال', 'بال', 'لل', 'فال', 'كال',         // 3-char
        'وب', 'فب', 'لب', 'وك', 'ول', 'وف',       // 2-char
        'ال', 'ب', 'و', 'ف', 'ل', 'ك',            // 1-2 char
    ];

    private const ARABIC_SUFFIXES = [
        'تين', 'ين', 'ون', 'ات', 'ان', 'ية',
        'كم', 'هم', 'ها', 'نا', 'ني', 'تي',
    ];

    private const ARABIC_STOP_WORDS = [
        'من','إلى','عن','على','في','مع','هذا','هذه','ذلك','تلك','هو','هي','هم',
        'نحن','أنا','أنت','أنتم','أنتن','هن','له','لها','لهم','بها','بهم','بهن',
        'منه','منها','منهم','إنه','إنها','إنهم','كان','كانت','كانوا','يكون',
        'يكونون','هل','ما','ماذا','لماذا','كيف','متى','أين','أو','إما','بل',
        'لكن','لذا','لذلك','لأن','لأنه','لأنها','وقد','قد','قال','قالت','يقول',
        'يجب','ينبغي','يمكن','لا','لم','لن','ليس','ليست','حتى','إذا','إذ',
        'بعد','قبل','خلال','حول','تحت','فوق','بين','عند','لدى','مثل','نفس',
        'كل','بعض','جميع','أي','أكثر','أقل','جداً','جدا','فقط','أيضاً','أيضا',
        'هناك','هنا','الآن','الان','اليوم','غداً','أمس','دائماً','دائما',
        'أحيانا','أبدا','أولا','ثانيا','ثم','بعدها','سوف','لقد','قد',
        'عليه','عليها','عليهم','إليه','إليها','إليهم','يا','أن','إن','إلا',
        'ما','مما','مهما','حيث','كما','مع','دون','بدون','رغم','تقريبا',
        'حوالي','نعم','كلا',
    ];

    private const ENGLISH_STOP_WORDS = [
        'the','a','an','is','it','in','on','at','to','of','and','or','but','for',
        'with','how','what','when','where','who','why','should','can','do','did',
        'was','has','have','this','that','be','are','he','she','we','you','i',
        'they','my','your','our','their','not','no','yes','will','would','could',
        'about','from','as','by','if','which','than','then','so','up','out',
        'had','him','his','her','its','been','being','were','just','also','more',
        'all','one','two','may','each','only','very','here','there','now','any',
        'does','did','am','doing','done','until','while','into','through',
        'during','before','after','above','below','down','off','over','under',
        'again','further','once','both','few','most','other','some','such',
        'nor','own','same','too','very','s','t','d','ll','m','o','re','ve','y',
        'ain','aren','couldn','didn','doesn','hadn','hasn','haven','isn','ma',
        'mightn','mustn','needn','shan','shouldn','wasn','weren','won','wouldn',
        'um','uh','like','want','hello','hi','hey','talk','customer','agent',
    ];

    public function getKnowledgeMappingContext(int $companyId): string
    {
        $entries = $this->loadEntries($companyId);
        
        if ($entries->isEmpty()) {
            return "No knowledge base entries found.";
        }

        $mapping = [];
        foreach ($entries as $entry) {
            $mapping[] = [
                'name'     => $entry->title,
                'keywords' => array_map('trim', explode(',', $entry->keywords ?? ''))
            ];
        }

        return json_encode(['knowledge_bases' => $mapping], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function getRelevantContext(
        int    $companyId,
        string $queryText,
        int    $maxChunks = 4,
        int    $chunkSize = 2000,
        int    $overlap   = 250
    ): ?string {
        $entries = $this->loadEntries($companyId);

        if ($entries->isEmpty()) {
            return null;
        }

        $keywords = $this->extractKeywords($queryText);

        if (empty($keywords)) {
            return null;
        }

        $allChunks = [];
        foreach ($entries as $entry) {
            $chunks = $this->chunkText($entry->content, $chunkSize, $overlap);
            foreach ($chunks as $chunk) {
                $allChunks[] = [
                    'title'    => $entry->title,
                    'content'  => $chunk,
                    'keywords' => $entry->keywords ?? '', // Pass keywords to include in scoring
                ];
            }
        }

        if (empty($allChunks)) {
            return null;
        }

        $allChunkTexts = array_column($allChunks, 'content');
        $scoredChunks  = [];

        foreach ($allChunks as $chunk) {
            $score = $this->scoreChunkTfIdf($chunk['content'], $chunk['title'], $chunk['keywords'], $keywords, $allChunkTexts);
            if ($score > 0) {
                $scoredChunks[] = [
                    'title'   => $chunk['title'],
                    'content' => $chunk['content'],
                    'score'   => $score,
                ];
            }
        }

        if (empty($scoredChunks)) {
            return null;
        }

        usort($scoredChunks, fn($a, $b) => $b['score'] <=> $a['score']);
        $scoredChunks = $this->deduplicateChunks($scoredChunks);
        $topChunks    = array_slice($scoredChunks, 0, $maxChunks);

        return $this->buildContextString($topChunks);
    }

    public function getRelevantContextWithMeta(
        int    $companyId,
        string $queryText,
        int    $maxChunks = 4
    ): array {
        $keywords = $this->extractKeywords($queryText);
        $context  = $this->getRelevantContext($companyId, $queryText, $maxChunks);

        return [
            'keywords_found' => $keywords,
            'matched'        => $context !== null,
            'context'        => $context,
        ];
    }

    public function invalidateCache(int $companyId): void
    {
        Cache::forget("kb_entries_{$companyId}");
    }

    private function scoreChunkTfIdf(
        string $chunkContent,
        string $entryTitle,
        string $entryKeywords,
        array  $keywords,
        array  $allChunkTexts
    ): float {
        $normalizedChunk    = $this->normalizeArabic(mb_strtolower($chunkContent));
        $normalizedTitle    = $this->normalizeArabic(mb_strtolower($entryTitle));
        $normalizedKeywords = $this->normalizeArabic(mb_strtolower($entryKeywords));
        
        $chunkWordCount  = max(1, count(preg_split('/\s+/u', trim($normalizedChunk))));
        $totalChunks     = count($allChunkTexts);
        $score           = 0.0;

        foreach ($keywords as $keyword) {
            $bodyCount    = mb_substr_count($normalizedChunk, $keyword);
            $titleCount   = mb_substr_count($normalizedTitle, $keyword);
            $keywordCount = mb_substr_count($normalizedKeywords, $keyword);

            if ($bodyCount === 0 && $titleCount === 0 && $keywordCount === 0) {
                continue;
            }

            // Heavily weight keyword matches (6x) and title matches (4x)
            $tf = ($bodyCount + ($titleCount * 4) + ($keywordCount * 6)) / $chunkWordCount;

            $docsWithTerm = 0;
            foreach ($allChunkTexts as $otherChunk) {
                if (mb_substr_count($this->normalizeArabic(mb_strtolower($otherChunk)), $keyword) > 0) {
                    $docsWithTerm++;
                }
            }

            $idf    = log(($totalChunks + 1) / ($docsWithTerm + 1)) + 1;
            $score += $tf * $idf;
        }

        $keywordCoverage = 0;
        foreach ($keywords as $keyword) {
            if (mb_substr_count($normalizedChunk, $keyword) > 0 ||
                mb_substr_count($normalizedTitle, $keyword) > 0 ||
                mb_substr_count($normalizedKeywords, $keyword) > 0) {
                $keywordCoverage++;
            }
        }

        $coverageBonus = count($keywords) > 0
            ? ($keywordCoverage / count($keywords)) * 0.5
            : 0;

        return round($score + $coverageBonus, 6);
    }

    private function extractKeywords(string $text): array
    {
        $text = $this->normalizeArabic($text);

        $tokens = preg_split(
            '/[\s\.,،؛؟?!:;()\[\]"\'«»\-\/\\\\|_@#%^&*+=~`]+/u',
            mb_strtolower($text),
            -1,
            PREG_SPLIT_NO_EMPTY
        );

        $keywords = [];

        foreach ($tokens as $word) {
            if (mb_strlen($word) < 2) {
                continue;
            }

            if ($this->isArabicWord($word)) {
                $stem = $this->stripArabicPrefixes($word);
                $stem = $this->stripArabicSuffixes($stem);

                if (mb_strlen($stem) < 2) {
                    continue;
                }

                if (in_array($stem, self::ARABIC_STOP_WORDS, true)) {
                    continue;
                }

                $keywords[] = $stem;

                if ($stem !== $word && !in_array($word, self::ARABIC_STOP_WORDS, true)) {
                    $keywords[] = $word;
                }

            } else {
                if (in_array($word, self::ENGLISH_STOP_WORDS, true)) {
                    continue;
                }
                $keywords[] = $word;
            }
        }

        return array_values(array_unique($keywords));
    }

    private function isArabicWord(string $word): bool
    {
        return (bool) preg_match('/[\x{0600}-\x{06FF}]/u', $word);
    }

    private function normalizeArabic(string $text): string
    {
        $text = preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $text);
        $text = preg_replace('/[إأآٱ]/u', 'ا', $text);
        $text = preg_replace('/ة/u', 'ه', $text);
        $text = preg_replace('/ى/u', 'ي', $text);

        return $text;
    }

    private function stripArabicPrefixes(string $word): string
    {
        foreach (self::ARABIC_PREFIXES as $prefix) {
            $prefixLen = mb_strlen($prefix);

            if (mb_substr($word, 0, $prefixLen) !== $prefix) {
                continue;
            }

            $stripped     = mb_substr($word, $prefixLen);
            $minRemainder = $prefixLen === 1 ? 3 : 2;

            if (mb_strlen($stripped) >= $minRemainder) {
                return $stripped;
            }
        }

        return $word;
    }

    private function stripArabicSuffixes(string $word): string
    {
        foreach (self::ARABIC_SUFFIXES as $suffix) {
            $len = mb_strlen($suffix);
            if (mb_strlen($word) > ($len + 2) && mb_substr($word, -$len) === $suffix) {
                return mb_substr($word, 0, -$len);
            }
        }

        return $word;
    }

    private function chunkText(string $text, int $size, int $overlap): array
    {
        $text   = trim($text);
        $length = mb_strlen($text);

        if ($length === 0) {
            return [];
        }

        if ($length <= $size) {
            return [$text];
        }

        $chunks = [];
        $start  = 0;

        while ($start < $length) {
            $chunk = mb_substr($text, $start, $size);

            if ($start + $size < $length && mb_strlen($chunk) >= (int)($size * 0.6)) {
                $boundary = $this->findLastSentenceBoundary($chunk);
                if ($boundary !== false && $boundary > (int)($size * 0.4)) {
                    $chunk = mb_substr($chunk, 0, $boundary + 1);
                }
            }

            $chunkLen = mb_strlen($chunk);
            if ($chunkLen > 0) {
                $chunks[] = $chunk;
            }

            $step  = max($chunkLen - $overlap, 50);
            $start += $step;
        }

        return $chunks;
    }

    private function findLastSentenceBoundary(string $text): int|false
    {
        $markers   = ['.', '؟', '!', '?', '\n'];
        $positions = [];

        foreach ($markers as $marker) {
            $pos = mb_strrpos($text, $marker);
            if ($pos !== false) {
                $positions[] = $pos;
            }
        }

        return empty($positions) ? false : max($positions);
    }

    private function deduplicateChunks(array $chunks): array
    {
        $unique = [];

        foreach ($chunks as $chunk) {
            $isDuplicate = false;
            $preview     = mb_substr($chunk['content'], 0, 500);

            foreach ($unique as $accepted) {
                similar_text($preview, mb_substr($accepted['content'], 0, 500), $percent);
                if ($percent > 80) {
                    $isDuplicate = true;
                    break;
                }
            }

            if (!$isDuplicate) {
                $unique[] = $chunk;
            }
        }

        return $unique;
    }

    private function loadEntries(int $companyId)
    {
        return Cache::remember("kb_entries_{$companyId}", 300, function () use ($companyId) {
            return KnowledgeBase::where('company_id', $companyId)
                ->where('is_active', true)
                ->whereNotNull('content')
                ->where('content', '!=', '')
                ->select(['id', 'title', 'content', 'keywords'])
                ->get();
        });
    }

    private function buildContextString(array $topChunks): string
    {
        $context = "";

        foreach ($topChunks as $chunk) {
            $context .= trim($chunk['content']) . "\n\n";
        }

        return trim($context);
    }
}