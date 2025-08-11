<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;
use App\Models\Feature;
use Illuminate\Support\Str;

class PlanFeatureSeeder extends Seeder
{
    public function run()
    {
        // Create plans
        $plans = [
            [
                'name' => 'Free',
                'description' => 'Exploring Creative AI',
                'price' => 0,
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Starter',
                'description' => 'For small teams',
                'price' => 299,
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Business',
                'description' => 'Growing teams',
                'price' => 999,
                'is_popular' => true,
                'is_active' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'Premium',
                'description' => 'Advanced features',
                'price' => 2999,
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 4
            ],
        ];

        foreach ($plans as $planData) {
            Plan::create($planData);
        }

        // Create features
        $features = [
            ['name' => 'Text Responses', 'key' => 'text_responses', 'type' => 'integer', 'sort_order' => 1],
            ['name' => 'Voice Minutes', 'key' => 'voice_minutes', 'type' => 'integer', 'sort_order' => 2],
            ['name' => 'AI Agents', 'key' => 'ai_agents', 'type' => 'integer', 'sort_order' => 3],
            ['name' => 'Voice Recordings', 'key' => 'voice_recordings', 'type' => 'boolean', 'sort_order' => 4],
            ['name' => 'Integrations', 'key' => 'integrations', 'type' => 'string', 'sort_order' => 5],
            ['name' => 'White Label', 'key' => 'white_label', 'type' => 'integer', 'sort_order' => 6],
            ['name' => 'Analytics', 'key' => 'analytics', 'type' => 'string', 'sort_order' => 7],
            ['name' => 'API Access', 'key' => 'api_access', 'type' => 'integer', 'sort_order' => 8],
            ['name' => 'Onboarding', 'key' => 'onboarding', 'type' => 'boolean', 'sort_order' => 9],
            ['name' => 'SLAs', 'key' => 'slas', 'type' => 'boolean', 'sort_order' => 10],
        ];

        foreach ($features as $featureData) {
            Feature::create($featureData);
        }

        // Get all plans and features
        $planModels = Plan::all()->keyBy('name');
        $featureModels = Feature::all()->keyBy('key');

        // Attach features to plans with values
        $this->attachFeatures($planModels, $featureModels);
    }

    protected function attachFeatures($plans, $features)
    {
        // Free Plan
        $plans['Free']->features()->attach([
            $features['text_responses']->id => ['value' => '100'],
            $features['voice_minutes']->id => ['value' => '50'],
            $features['ai_agents']->id => ['value' => '1'],
            $features['voice_recordings']->id => ['value' => '0'],
            $features['integrations']->id => ['value' => 'Basic'],
            $features['white_label']->id => ['value' => '200'],
            $features['analytics']->id => ['value' => 'Limited'],
            $features['api_access']->id => ['value' => '5'],
            $features['onboarding']->id => ['value' => '0'],
            $features['slas']->id => ['value' => '0'],
        ]);

        // Starter Plan
        $plans['Starter']->features()->attach([
            $features['text_responses']->id => ['value' => '2000'],
            $features['voice_minutes']->id => ['value' => '500'],
            $features['ai_agents']->id => ['value' => '2'],
            $features['voice_recordings']->id => ['value' => '0'],
            $features['integrations']->id => ['value' => 'Basic'],
            $features['white_label']->id => ['value' => '0'],
            $features['analytics']->id => ['value' => 'Basic'],
            $features['api_access']->id => ['value' => '0'],
            $features['onboarding']->id => ['value' => '0'],
            $features['slas']->id => ['value' => '0'],
        ]);

        // Business Plan
        $plans['Business']->features()->attach([
            $features['text_responses']->id => ['value' => '7500'],
            $features['voice_minutes']->id => ['value' => '1500'],
            $features['ai_agents']->id => ['value' => '5'],
            $features['voice_recordings']->id => ['value' => '1'],
            $features['integrations']->id => ['value' => 'Advanced'],
            $features['white_label']->id => ['value' => '0'],
            $features['analytics']->id => ['value' => 'Advanced'],
            $features['api_access']->id => ['value' => '1'],
            $features['onboarding']->id => ['value' => '1'],
            $features['slas']->id => ['value' => '0'],
        ]);

        // Premium Plan
        $plans['Premium']->features()->attach([
            $features['text_responses']->id => ['value' => '30000'],
            $features['voice_minutes']->id => ['value' => '5500'],
            $features['ai_agents']->id => ['value' => '15'],
            $features['voice_recordings']->id => ['value' => '1'],
            $features['integrations']->id => ['value' => 'Advanced'],
            $features['white_label']->id => ['value' => '1'],
            $features['analytics']->id => ['value' => 'Advanced'],
            $features['api_access']->id => ['value' => '1'],
            $features['onboarding']->id => ['value' => '1'],
            $features['slas']->id => ['value' => '0'],
        ]);
    }
}