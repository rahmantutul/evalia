<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evalia - AI Audio QA</title>
    <meta name="description" content="Evalia analyzes calls, scores quality, flags risk, and helps managers coach agents with clear evidence.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #060d1f;
            --navy-2: #0f172a;
            --slate: #475569;
            --slate-2: #64748b;
            --line: #e2e8f0;
            --soft: #f8fafc;
            --cyan: #06b6d4;
            --cyan-2: #22d3ee;
            --amber: #f59e0b;
            --emerald: #10b981;
            --rose: #fb7185;
            --white: #ffffff;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--navy-2);
            background: var(--white);
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        p,
        h1,
        h2,
        h3 {
            margin: 0;
        }

        .container {
            width: min(1180px, calc(100% - 48px));
            margin: 0 auto;
        }

        /* ─── NAV ─── */
        .nav {
            position: fixed;
            inset: 0 0 auto;
            z-index: 20;
            background: rgba(6, 13, 31, 0.9);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(14px);
            animation: navGlow 4s ease-in-out infinite;
        }
        @keyframes navGlow {
            0%, 100% { border-bottom-color: rgba(6, 182, 212, 0.1); }
            50% { border-bottom-color: rgba(6, 182, 212, 0.4); }
        }
        .nav-inner {
            min-height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 28px;
        }
        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            color: var(--white);
            font-size: 20px;
            font-weight: 900;
            letter-spacing: -0.02em;
        }
        .brand-logo {
            height: 40px;
            width: auto;
            object-fit: contain;
            transition: transform 0.3s, opacity 0.3s;
        }
        .brand-logo:hover {
            transform: scale(1.05);
            opacity: 0.9;
        }
        .nav-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 10px;
            border-radius: 7px;
            color: #67e8f9;
            background: rgba(6, 182, 212, 0.14);
            font-size: 10px;
            letter-spacing: 0;
            text-transform: lowercase;
            animation: pillPulse 2s ease-in-out infinite;
        }
        @keyframes pillPulse {
            0%, 100% { background: rgba(6, 182, 212, 0.14); }
            50% { background: rgba(6, 182, 212, 0.3); }
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
            color: #cbd5e1;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .nav-links a {
            position: relative;
            transition: color 0.3s;
        }
        .nav-links a::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -4px;
            width: 0;
            height: 2px;
            background: var(--cyan);
            transition: width 0.3s;
        }
        .nav-links a:hover::after {
            width: 100%;
        }
        .nav-links a:hover {
            color: var(--white);
        }
        .login-link,
        .primary-btn,
        .secondary-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 800;
            transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
            cursor: pointer;
            border: none;
        }
        .primary-btn {
            padding: 0 26px;
            color: var(--white);
            background: linear-gradient(135deg, var(--cyan), #2563eb);
            background-size: 200% 200%;
            box-shadow: 0 16px 30px rgba(6, 182, 212, 0.22);
            animation: btnFloat 3s ease-in-out infinite, btnGradient 4s ease-in-out infinite;
            position: relative;
            overflow: hidden;
        }
        .login-link::before,
        .primary-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(135deg, rgba(255,255,255,0.2), transparent 50%);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .login-link:hover::before,
        .primary-btn:hover::before {
            opacity: 1;
        }
        .login-link {
            min-height: 38px;
            padding: 0 18px;
            color: var(--white);
            border: 1px solid rgba(103, 232, 249, 0.26);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            box-shadow: none;
            animation: none;
            position: relative;
            overflow: hidden;
        }
        .login-link::before {
            display: none;
        }
        @keyframes btnGradient {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        @keyframes btnFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-4px); }
        }
        .login-link:hover,
        .primary-btn:hover {
            background: #0891b2;
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 20px 36px rgba(6, 182, 212, 0.35);
            animation-play-state: paused;
        }
        .login-link:hover {
            color: var(--white);
            border-color: rgba(103, 232, 249, 0.48);
            background: rgba(103, 232, 249, 0.12);
            transform: translateY(-1px);
            box-shadow: none;
        }
        .secondary-btn {
            padding: 0 22px;
            color: #e2e8f0;
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(255, 255, 255, 0.04);
            animation: btnFloat2 3.5s ease-in-out infinite 0.5s;
        }
        @keyframes btnFloat2 {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-4px); }
        }
        .secondary-btn:hover {
            color: var(--white);
            border-color: rgba(255, 255, 255, 0.32);
            transform: translateY(-3px);
            animation-play-state: paused;
        }
        .hero .secondary-btn {
            color: #e2e8f0;
            border-color: rgba(255, 255, 255, 0.22);
            background: rgba(255, 255, 255, 0.08);
            animation: none;
            box-shadow: none;
        }
        .hero .primary-btn {
            animation: none;
            background: linear-gradient(135deg, #0891b2, #2563eb);
            box-shadow: 0 16px 34px rgba(6, 182, 212, 0.22);
        }
        .hero .secondary-btn:hover {
            color: var(--white);
            border-color: rgba(103, 232, 249, 0.42);
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 16px 34px rgba(2, 6, 23, 0.18);
        }

        /* ─── HERO ─── */
        .hero {
            padding: 148px 0 92px;
            color: var(--white);
            background:
                linear-gradient(90deg, rgba(2, 6, 23, 0.94) 0%, rgba(2, 6, 23, 0.82) 44%, rgba(2, 6, 23, 0.46) 100%),
                linear-gradient(180deg, rgba(2, 6, 23, 0.08), rgba(2, 6, 23, 0.34)),
                url("{{ asset('assets/images/audio-analysis-login-bg.png') }}") center / cover no-repeat;
            overflow: hidden;
            position: relative;
        }

        .hero-noise {
            position: absolute;
            inset: 0;
            opacity: 0.018;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
            background-size: 180px;
            pointer-events: none;
            z-index: 1;
        }

        .hero-grid-bg {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.035) 1px, transparent 1px);
            background-size: 72px 72px;
            mask-image: linear-gradient(180deg, black 0%, transparent 78%);
            -webkit-mask-image: linear-gradient(180deg, black 0%, transparent 78%);
            pointer-events: none;
            z-index: 1;
        }

        .hero-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            pointer-events: none;
            will-change: transform;
        }
        .hero-orb--1 {
            width: 520px;
            height: 520px;
            top: -22%;
            right: -12%;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.12), transparent 68%);
        }
        .hero-orb--2 {
            width: 420px;
            height: 420px;
            bottom: -28%;
            left: -12%;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.08), transparent 70%);
        }
        .hero-orb--3 {
            display: none;
        }
        .hero-orb--4 {
            display: none;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 0.9fr) minmax(420px, 1.1fr);
            align-items: center;
            gap: 64px;
            position: relative;
            z-index: 2;
        }

        .eyebrow {
            margin-bottom: 20px;
            color: #67e8f9;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: 1px solid rgba(103, 232, 249, 0.24);
            border-radius: 999px;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.18);
        }
        .eyebrow::before {
            content: '';
            width: 7px;
            height: 7px;
            background: var(--cyan);
            border-radius: 50%;
        }

        h1 {
            max-width: 680px;
            font-size: clamp(38px, 5vw, 62px);
            line-height: 1.06;
            letter-spacing: 0;
            font-weight: 900;
            color: var(--white);
            opacity: 0;
            animation: heroRevealUp 0.7s ease 0.2s forwards;
        }
        h1 .highlight {
            background: linear-gradient(135deg, #0891b2, #2563eb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-copy {
            max-width: 590px;
            margin-top: 22px;
            color: #cbd5e1;
            font-size: 18px;
            line-height: 1.7;
            opacity: 0;
            animation: heroRevealUp 0.7s ease 0.34s forwards;
        }
        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 32px;
            opacity: 0;
            animation: heroRevealUp 0.7s ease 0.46s forwards;
        }
        @keyframes heroRevealUp {
            0% { opacity: 0; transform: translateY(18px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* ─── DASHBOARD ─── */
        .dashboard-wrapper {
            position: relative;
            opacity: 0;
            animation: heroRevealUp 0.8s ease 0.32s forwards;
        }

        .dashboard-wrapper::before {
            content: '';
            position: absolute;
            inset: 22px -18px -18px 26px;
            border-radius: 28px;
            background: rgba(6, 182, 212, 0.08);
            z-index: -1;
            border: 1px solid rgba(6, 182, 212, 0.12);
        }

        .dashboard-wrapper::after {
            content: '';
            position: absolute;
            inset: auto 8% -34px 8%;
            height: 42px;
            border-radius: 50%;
            background: rgba(15, 23, 42, 0.16);
            filter: blur(26px);
            z-index: -2;
        }

        .dashboard-shell {
            border-radius: 24px;
            border: 1px solid rgba(15, 23, 42, 0.1);
            background: rgba(255, 255, 255, 0.78);
            padding: 12px;
            box-shadow:
                0 32px 70px rgba(15, 23, 42, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.7);
        }
        .dashboard {
            border-radius: 18px;
            border: 1px solid rgba(15, 23, 42, 0.08);
            background: #ffffff;
            padding: 22px;
            color: #0f172a;
            position: relative;
            overflow: hidden;
        }
        .dashboard::after {
            display: none;
        }
        .dashboard-head {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--line);
        }
        .dashboard-head h2 {
            font-size: 16px;
            font-weight: 900;
        }
        .dashboard-head p {
            margin-top: 5px;
            color: #64748b;
            font-size: 12px;
        }
        .status {
            height: 27px;
            white-space: nowrap;
            border-radius: 999px;
            padding: 6px 12px;
            color: #0e7490;
            background: #ecfeff;
            font-size: 12px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .status::before {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--cyan);
            animation: statusPulse 1.2s ease-in-out infinite;
        }
        @keyframes statusPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.2; transform: scale(0.6); }
        }

        .metric-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-top: 18px;
        }
        .metric {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 16px;
            background: #f8fafc;
            transition: all 0.3s;
            cursor: default;
        }
        .metric:hover {
            background: #ffffff;
            transform: translateY(-4px);
            box-shadow: 0 12px 26px rgba(15, 23, 42, 0.08);
        }
        .metric span {
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }
        .metric strong {
            display: block;
            margin-top: 9px;
            font-size: 30px;
            line-height: 1;
            font-weight: 900;
            transition: color 0.3s;
        }
        .metric:hover strong {
            color: var(--cyan-2);
        }
        /* Animated counters */
        .metric strong[data-count] {
            animation: countUp 2s ease-out forwards;
        }
        @keyframes countUp {
            0% { opacity: 0; transform: scale(0.5); }
            100% { opacity: 1; transform: scale(1); }
        }

        .report-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 16px;
            margin-top: 16px;
        }
        .transcript,
        .risk-card,
        .coach-card {
            border-radius: 12px;
            padding: 16px;
            background: #f8fafc;
            transition: all 0.3s;
        }
        .transcript:hover,
        .risk-card:hover,
        .coach-card:hover {
            background: #ffffff;
            transform: translateY(-3px);
        }
        .transcript-title {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 14px;
            color: #64748b;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }
        .line {
            margin-top: 10px;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 12px;
            background: #ffffff;
            transition: all 0.3s;
        }
        .line:hover {
            border-color: rgba(6, 182, 212, 0.3);
            background: #ecfeff;
        }
        .line.warning {
            border-color: rgba(245, 158, 11, 0.35);
            background: rgba(245, 158, 11, 0.11);
            animation: warningGlow 2s ease-in-out infinite;
        }
        @keyframes warningGlow {
            0%, 100% { border-color: rgba(245, 158, 11, 0.35); }
            50% { border-color: rgba(245, 158, 11, 0.7); }
        }
        .line.warning:hover {
            border-color: rgba(245, 158, 11, 0.6);
            background: rgba(245, 158, 11, 0.18);
        }
        .speaker {
            display: block;
            margin-bottom: 4px;
            color: #64748b;
            font-size: 11px;
            font-weight: 800;
        }
        .line p {
            color: #334155;
            font-size: 13px;
            line-height: 1.65;
        }
        .side-stack {
            display: grid;
            gap: 16px;
        }
        .risk-card {
            border: 1px solid rgba(251, 113, 133, 0.25);
            background: #fff1f2;
        }
        .risk-card:hover {
            border-color: rgba(251, 113, 133, 0.5);
        }
        .coach-card {
            border: 1px solid rgba(16, 185, 129, 0.25);
            background: #ecfdf5;
        }
        .coach-card:hover {
            border-color: rgba(16, 185, 129, 0.5);
        }
        .risk-card h3,
        .coach-card h3 {
            margin-bottom: 8px;
            font-size: 14px;
        }
        .risk-card h3 { color: #be123c; }
        .coach-card h3 { color: #047857; }
        .risk-card p,
        .coach-card p {
            color: #334155;
            font-size: 13px;
            line-height: 1.65;
        }

        /* ─── FLOATING BADGES ─── */
        .floating-badge {
            position: absolute;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            color: var(--white);
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.3);
            z-index: 5;
            opacity: 1;
            white-space: nowrap;
        }
        .floating-badge--score {
            top: 8%;
            right: -12px;
            animation-delay: 1s, 0s;
        }
        .floating-badge--risk {
            bottom: 22%;
            left: -18px;
            animation-delay: 1.3s, 0.5s;
        }
        .floating-badge--coach {
            bottom: 5%;
            right: 5%;
            animation-delay: 1.6s, 1s;
        }
        @keyframes badgeAppear {
            0% { opacity: 0; transform: scale(0.6) translateY(10px); filter: blur(4px); }
            100% { opacity: 1; transform: scale(1) translateY(0); filter: blur(0); }
        }
        @keyframes badgeFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-6px); }
        }
        .badge-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            animation: dotPulse 2s ease-in-out infinite;
        }
        .badge-dot--green {
            background: var(--emerald);
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
        }
        .badge-dot--rose {
            background: var(--rose);
            box-shadow: 0 0 10px rgba(251, 113, 133, 0.5);
        }
        .badge-dot--cyan {
            background: var(--cyan);
            box-shadow: 0 0 10px rgba(6, 182, 212, 0.5);
        }
        @keyframes dotPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.7); }
        }

        /* ─── TRACKING DASHBOARD SECTIONS ─── */
        .section { padding: 88px 0; position: relative; }
        .section.soft {
            border-top: 1px solid var(--line);
            border-bottom: 1px solid var(--line);
            background: var(--soft);
        }
        .section.dark { color: var(--white); background: #020617; }
        .section-heading { max-width: 700px; margin-bottom: 48px; }
        .section-heading.center { margin-left: auto; margin-right: auto; text-align: center; }
        .section-kicker {
            margin: 0 0 18px;
            color: var(--cyan);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.22em;
            text-transform: uppercase;
        }
        .section-heading .section-kicker {
            margin: 0 0 18px;
            color: var(--cyan);
        }
        .section-heading h2,
        .solo-heading,
        .cta h2 {
            margin: 0;
            font-size: clamp(28px, 4vw, 46px);
            line-height: 1.1;
            letter-spacing: -0.035em;
            font-weight: 800;
        }
        .section-heading p,
        .cta p {
            margin: 16px 0 0;
            color: var(--slate);
            font-size: 16px;
            line-height: 1.7;
        }
        .section.dark .section-heading p { color: #94a3b8; }

        .how-split {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 48px;
            align-items: center;
            margin-top: 52px;
        }
        .how-visual {
            position: relative;
        }
        .how-visual-inner {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            background: linear-gradient(145deg, #f0fdfa, #ecfeff);
            border: 1px solid var(--line);
            aspect-ratio: 4/3;
        }
        .how-hero-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .how-float-card {
            position: absolute;
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 600;
            color: var(--navy-2);
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            animation: floatBounce 3s ease-in-out infinite;
        }
        .how-float-card--top { top: 20px; right: 20px; }
        .how-float-card--bottom { bottom: 20px; left: 20px; animation-delay: 1s; }
        .how-float-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ef4444;
            animation: pulse 1.5s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
        @keyframes floatBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }

        .how-steps {
            display: flex;
            flex-direction: column;
            gap: 0;
        }
        .how-step {
            display: flex;
            gap: 20px;
            padding: 20px 0;
            border-bottom: 1px solid var(--line);
            transition: background 0.2s ease;
        }
        .how-step:last-child { border-bottom: none; }
        .how-step:hover { background: rgba(6,182,212,0.02); }
        .how-step-left {
            position: relative;
            flex-shrink: 0;
        }
        .how-step-circle {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .how-step:hover .how-step-circle { transform: scale(1.06); }
        .how-step-num {
            position: absolute;
            top: -6px;
            right: -6px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--white);
            border: 2px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 800;
            color: var(--navy-2);
        }
        .how-step-text h3 {
            margin: 0;
            font-size: 17px;
            font-weight: 700;
            color: var(--navy-2);
            letter-spacing: -0.01em;
        }
        .how-step-text p {
            margin: 4px 0 0;
            color: var(--slate);
            font-size: 14px;
            line-height: 1.65;
        }

        #report {
            background:
                linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border-top: 1px solid var(--line);
            border-bottom: 1px solid var(--line);
        }
        #report .section-heading {
            margin-bottom: 34px;
        }
        #report .section-heading h2 {
            max-width: 680px;
        }
        .inside-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.25fr) repeat(2, minmax(0, 0.8fr));
            gap: 16px;
            position: relative;
            padding: 18px;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.72);
            box-shadow: 0 24px 70px rgba(15, 23, 42, 0.08);
        }
        .inside-grid::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background:
                linear-gradient(90deg, rgba(6, 182, 212, 0.08), transparent 34%),
                linear-gradient(180deg, rgba(15, 23, 42, 0.025), transparent 42%);
            pointer-events: none;
        }
        .summary-card,
        .stat-card {
            position: relative;
            z-index: 1;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 26px;
            background: var(--white);
            transition: all 0.3s ease;
        }
        .summary-card:hover,
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 34px rgba(15, 23, 42, 0.08);
        }
        .summary-card {
            grid-row: span 2;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 330px;
            color: var(--white);
            border-color: rgba(15, 23, 42, 0.12);
            background:
                linear-gradient(135deg, rgba(8, 145, 178, 0.92), rgba(15, 23, 42, 0.96)),
                #0f172a;
            overflow: hidden;
        }
        .summary-card::after {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            right: -96px;
            bottom: -104px;
            border-radius: 50%;
            background: rgba(34, 211, 238, 0.16);
        }
        .summary-card .label,
        .stat-card .label {
            color: #64748b;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.11em;
            text-transform: uppercase;
        }
        .summary-card .label {
            color: #a5f3fc;
        }
        .summary-card h3 {
            margin-top: 18px;
            max-width: 430px;
            font-size: 28px;
            line-height: 1.16;
            letter-spacing: 0;
            font-weight: 800;
        }
        .summary-card p,
        .stat-card p {
            margin-top: 10px;
            color: var(--slate);
            font-size: 13px;
            line-height: 1.7;
        }
        .summary-card > p:not(.label) {
            max-width: 470px;
            color: #cbd5e1;
            font-size: 14px;
        }
        .recommendation {
            margin-top: 22px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-left: 3px solid var(--cyan-2);
            border-radius: 10px;
            padding: 16px 18px;
            background: rgba(255, 255, 255, 0.08);
        }
        .recommendation b {
            display: block;
            color: #67e8f9;
            font-size: 12px;
            margin-bottom: 7px;
        }
        .recommendation span {
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.6;
        }
        .stat-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            color: #0891b2;
            background:
                linear-gradient(135deg, rgba(6, 182, 212, 0.18), rgba(37, 99, 235, 0.08));
            margin-bottom: 16px;
            border: 1px solid rgba(6, 182, 212, 0.2);
            transition: transform 0.3s ease, opacity 0.3s ease;
        }
        .stat-icon svg {
            width: 20px;
            height: 20px;
        }
        .stat-card:hover .stat-icon { transform: scale(1.06); opacity: 1; }
        .stat-card strong {
            display: block;
            margin-top: 18px;
            color: var(--navy-2);
            font-size: 34px;
            line-height: 1;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
            transition: color 0.3s ease;
        }
        .stat-card:hover strong { color: var(--cyan); }

        .dark-wrap {
            position: relative;
            padding: 48px 0;
        }
        .dark-wrap::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 50% 40%, rgba(6, 182, 212, 0.08), transparent 70%),
                radial-gradient(ellipse 40% 40% at 20% 70%, rgba(99, 102, 241, 0.04), transparent),
                radial-gradient(ellipse 40% 40% at 80% 30%, rgba(16, 185, 129, 0.04), transparent);
            pointer-events: none;
        }
        .dark-heading {
            text-align: center;
            margin-bottom: 52px;
            position: relative;
            z-index: 1;
        }
        .dark-heading .section-kicker {
            margin-bottom: 14px;
        }
        .dark-heading h2 {
            font-size: clamp(26px, 3.5vw, 40px);
            line-height: 1.15;
            letter-spacing: -0.03em;
            color: var(--white);
        }
        .output-flow {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1fr auto 1fr auto 1fr;
            gap: 0;
            align-items: stretch;
        }
        .output-card {
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 16px;
            padding: 32px 28px 28px;
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            transition: all 0.35s cubic-bezier(0.23, 1, 0.32, 1);
            overflow: hidden;
        }
        .output-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--cyan), transparent);
            opacity: 0;
            transition: opacity 0.35s ease;
        }
        .output-card:hover {
            background: rgba(255, 255, 255, 0.04);
            border-color: rgba(6, 182, 212, 0.15);
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -20px rgba(6, 182, 212, 0.12);
        }
        .output-card:hover::before {
            opacity: 1;
        }
        .output-step {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }
        .output-step-num {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            background: rgba(6, 182, 212, 0.12);
            color: #67e8f9;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }
        .output-step-label {
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }
        .output-card .icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            margin-bottom: 18px;
            font-size: 13px;
            font-weight: 800;
            transition: transform 0.3s ease;
        }
        .output-card:hover .icon {
            transform: scale(1.08);
        }
        .output-card:nth-child(1) .icon {
            color: #22d3ee;
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.15), rgba(6, 182, 212, 0.05));
            border: 1px solid rgba(6, 182, 212, 0.15);
        }
        .output-card:nth-child(2) .icon {
            color: #818cf8;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(99, 102, 241, 0.05));
            border: 1px solid rgba(99, 102, 241, 0.15);
        }
        .output-card:nth-child(3) .icon {
            color: #34d399;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.05));
            border: 1px solid rgba(16, 185, 129, 0.15);
        }
        .output-card h3 {
            margin-top: 0;
            color: var(--white);
            font-size: 19px;
            line-height: 1.3;
            font-weight: 800;
            letter-spacing: -0.01em;
        }
        .output-card p {
            margin-top: 10px;
            color: #94a3b8;
            font-size: 13.5px;
            line-height: 1.75;
        }
        .flow-arrow {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 16px;
            flex-direction: column;
            gap: 6px;
        }
        .flow-arrow svg {
            width: 32px;
            height: 32px;
            opacity: 0.3;
            transition: opacity 0.3s ease;
        }
        .output-flow:hover .flow-arrow svg {
            opacity: 0.5;
        }
        .flow-arrow-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #475569;
        }

        /* ─── TEAMS PORTALS ─── */
        .teams-section {
            position: relative;
            padding: 120px 0 140px;
            background: #f8fafc;
            overflow: hidden;
        }
        .teams-section::before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 50% 40% at 50% 50%, rgba(6,182,212,0.03), transparent);
            pointer-events: none;
        }
        .teams-section .section-heading {
            text-align: center;
            margin-bottom: 64px;
            position: relative;
            z-index: 2;
        }
        .teams-section .section-kicker {
            color: #0891b2;
            font-weight: 700;
        }
        .teams-section .section-heading h2 {
            font-size: clamp(28px, 4vw, 44px);
            line-height: 1.12;
            letter-spacing: -0.03em;
            color: #0f172a;
        }
        .teams-section .section-heading p {
            margin-top: 16px;
            color: #475569;
            font-size: 17px;
            line-height: 1.7;
            max-width: 520px;
            margin-left: auto;
            margin-right: auto;
            font-weight: 500;
        }

        .portal-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            position: relative;
            z-index: 2;
        }

        /* ── Portal Base ── */
        .portal {
            position: relative;
            border-radius: 20px;
            padding: 0;
            overflow: hidden;
            cursor: default;
            transition: transform 0.4s cubic-bezier(0.23, 1, 0.32, 1), box-shadow 0.4s ease, border-color 0.4s ease;
            background: #fff;
            border: 1px solid #e2e8f0;
        }
        .portal:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 48px -12px rgba(0,0,0,0.12);
        }

        .portal-bg {
            position: absolute;
            inset: 0;
            z-index: 0;
        }

        .portal-content {
            position: relative;
            z-index: 3;
            padding: 32px 28px 28px;
            display: flex;
            flex-direction: column;
        }
        .portal-features {
            list-style: none;
            margin-top: 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .portal-features li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13px;
            line-height: 1.5;
            color: #334155;
        }
        .portal-features li svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .portal-stats-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: auto;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
        }
        .portal-mini-stat strong {
            display: block;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1;
        }
        .portal-mini-stat small {
            display: block;
            margin-top: 4px;
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .portal-icon-wrap {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            margin-bottom: 18px;
            position: relative;
        }
        .portal-icon-wrap svg {
            width: 26px;
            height: 26px;
        }

        .portal-tag {
            display: inline-block;
            margin-bottom: 12px;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            width: fit-content;
        }
        .portal h3 {
            font-size: 20px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 8px;
            color: #0f172a;
        }
        .portal p {
            font-size: 14px;
            line-height: 1.65;
            max-width: 320px;
            color: #475569;
        }

        /* ── Portal 1: QA Managers ── */
        .portal--vault {
            background: #fff;
        }
        .portal--vault:hover {
            border-color: #bae6fd;
            box-shadow: 0 20px 48px -12px rgba(6,182,212,0.1);
        }
        .portal--vault .portal-icon-wrap {
            background: #f0fdfa;
            border: 1px solid #ccfbf1;
            color: #0891b2;
        }
        .portal--vault .portal-tag {
            color: #0891b2;
            background: #f0fdfa;
            border: 1px solid #ccfbf1;
        }
        .portal--vault .portal-mini-stat strong { color: #0891b2; }
        .portal--vault .portal-features li svg { stroke: #0891b2; }

        /* ── Portal 2: L&D Teams ── */
        .portal--grove {
            background: #fff;
        }
        .portal--grove:hover {
            border-color: #bae6fd;
            box-shadow: 0 20px 48px -12px rgba(6,182,212,0.1);
        }
        .portal--grove .portal-icon-wrap {
            background: #f0fdfa;
            border: 1px solid #ccfbf1;
            color: #0891b2;
        }
        .portal--grove .portal-tag {
            color: #0891b2;
            background: #f0fdfa;
            border: 1px solid #ccfbf1;
        }
        .portal--grove .portal-mini-stat strong { color: #0891b2; }
        .portal--grove .portal-features li svg { stroke: #0891b2; }

        /* ── Portal 3: CX Leaders ── */
        .portal--tide {
            background: #fff;
        }
        .portal--tide:hover {
            border-color: #bae6fd;
            box-shadow: 0 20px 48px -12px rgba(6,182,212,0.1);
        }
        .portal--tide .portal-icon-wrap {
            background: #f0fdfa;
            border: 1px solid #ccfbf1;
            color: #0891b2;
        }
        .portal--tide .portal-tag {
            color: #0891b2;
            background: #f0fdfa;
            border: 1px solid #ccfbf1;
        }
        .portal--tide .portal-mini-stat strong { color: #0891b2; }
        .portal--tide .portal-features li svg { stroke: #0891b2; }

        /* ── Portal Grid Responsive ── */
        .portal-grid::before,
        .portal-grid::after {
            display: none;
        }
        .outcome-panel {
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            gap: 48px;
            align-items: start;
        }
        .solo-heading {
            max-width: 760px;
            margin: 0 auto 42px;
            text-align: center;
        }

        .outcome-summary {
            min-height: 480px;
            padding: 0;
            color: var(--white);
            background: linear-gradient(160deg, #020617 0%, #0c1a3a 50%, #020617 100%);
            position: relative;
            overflow: hidden;
            border-radius: 28px;
            box-shadow:
                0 30px 70px rgba(15, 23, 42, 0.18),
                0 0 0 1px rgba(6, 182, 212, 0.08),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);
            transition: transform 0.4s cubic-bezier(0.23, 1, 0.32, 1), box-shadow 0.4s ease;
        }
        .outcome-summary::before {
            content: "";
            position: absolute;
            top: -120px;
            right: -120px;
            width: 280px;
            height: 280px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
            animation: outcomeGlow 6s ease-in-out infinite;
        }
        .outcome-summary::after {
            content: "";
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(250, 204, 21, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
            animation: outcomeGlow 6s ease-in-out infinite 3s;
        }
        @keyframes outcomeGlow {
            0%, 100% { opacity: 0.5; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.1); }
        }
        .outcome-image-shape {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
            border-radius: 16px;
        }
        .outcome-image-shape img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            filter: saturate(0.85);
            transition: filter 0.5s ease, transform 0.6s cubic-bezier(0.23, 1, 0.32, 1);
        }
        .outcome-summary:hover .outcome-image-shape img {
            filter: saturate(1);
            transform: scale(1.04);
        }
        /* Scan line */
        .outcome-scan {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent 0%, #22d3ee 30%, #67e8f9 50%, #22d3ee 70%, transparent 100%);
            box-shadow: 0 0 20px 6px rgba(34, 211, 238, 0.4), 0 0 60px 12px rgba(34, 211, 238, 0.15);
            z-index: 4;
            animation: scanDown 3s ease-in-out infinite;
            opacity: 0.9;
        }
        @keyframes scanDown {
            0% { top: 0%; opacity: 0; }
            10% { opacity: 0.9; }
            90% { opacity: 0.9; }
            100% { top: 100%; opacity: 0; }
        }
        /* Corner brackets */
        .outcome-bracket {
            position: absolute;
            width: 28px;
            height: 28px;
            z-index: 4;
            opacity: 0;
            transition: opacity 0.4s ease 0.1s;
        }
        .outcome-summary:hover .outcome-bracket {
            opacity: 1;
        }
        .outcome-bracket::before,
        .outcome-bracket::after {
            content: "";
            position: absolute;
            background: #67e8f9;
        }
        .outcome-bracket--tl { top: 12px; left: 12px; }
        .outcome-bracket--tl::before { top: 0; left: 0; width: 28px; height: 2px; }
        .outcome-bracket--tl::after { top: 0; left: 0; width: 2px; height: 28px; }
        .outcome-bracket--tr { top: 12px; right: 12px; }
        .outcome-bracket--tr::before { top: 0; right: 0; width: 28px; height: 2px; }
        .outcome-bracket--tr::after { top: 0; right: 0; width: 2px; height: 28px; }
        .outcome-bracket--bl { bottom: 12px; left: 12px; }
        .outcome-bracket--bl::before { bottom: 0; left: 0; width: 28px; height: 2px; }
        .outcome-bracket--bl::after { bottom: 0; left: 0; width: 2px; height: 28px; }
        .outcome-bracket--br { bottom: 12px; right: 12px; }
        .outcome-bracket--br::before { bottom: 0; right: 0; width: 28px; height: 2px; }
        .outcome-bracket--br::after { bottom: 0; right: 0; width: 2px; height: 28px; }
        /* Overlay gradient */
        .outcome-image-shape::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(2, 6, 23, 0.1) 0%, rgba(2, 6, 23, 0) 30%, rgba(2, 6, 23, 0) 60%, rgba(2, 6, 23, 0.85) 100%);
            z-index: 2;
            pointer-events: none;
        }
        /* Data wave canvas */
        .outcome-mesh {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 110px;
            z-index: 3;
            pointer-events: none;
            mask-image: linear-gradient(to top, rgba(0,0,0,1) 0%, rgba(0,0,0,0.8) 40%, transparent 100%);
            -webkit-mask-image: linear-gradient(to top, rgba(0,0,0,1) 0%, rgba(0,0,0,0.8) 40%, transparent 100%);
        }
        .outcome-mesh canvas {
            width: 100%;
            height: 100%;
            display: block;
        }
        .mesh-glow {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 80px;
            background: radial-gradient(ellipse at 50% 100%, rgba(34, 211, 238, 0.1) 0%, transparent 70%);
            z-index: 2;
            pointer-events: none;
        }
        .outcome-summary-text {
            padding: 32px 48px 40px;
            position: relative;
            z-index: 2;
        }
        .outcome-summary-text .section-kicker {
            margin-bottom: 16px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(6, 182, 212, 0.12);
            border: 1px solid rgba(6, 182, 212, 0.2);
            border-radius: 999px;
            color: #67e8f9;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }
        .outcome-summary-text .section-kicker::before {
            content: "";
            width: 6px;
            height: 6px;
            background: #67e8f9;
            border-radius: 50%;
            animation: kickerDot 2s ease-in-out infinite;
        }
        @keyframes kickerDot {
            0%, 100% { opacity: 0.4; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.3); }
        }
        .outcome-summary-text h3 {
            max-width: 390px;
            font-size: 30px;
            line-height: 1.2;
            letter-spacing: -0.03em;
            font-weight: 800;
            text-shadow: 0 2px 18px rgba(2, 6, 23, 0.55);
        }
        .outcome-summary-text h3 .yellow-highlight {
            color: #facc15;
            position: relative;
        }
        .outcome-summary-text h3 .yellow-highlight::after {
            content: "";
            position: absolute;
            bottom: 2px;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #facc15, rgba(250, 204, 21, 0.3));
            border-radius: 2px;
        }
        .outcome-summary-text p {
            max-width: 420px;
            margin-top: 14px;
            color: #e2e8f0;
            font-size: 15px;
            line-height: 1.75;
            text-shadow: 0 2px 14px rgba(2, 6, 23, 0.5);
        }
        .outcome-summary-text p .yellow-highlight {
            color: #facc15;
            font-weight: 600;
        }
        .outcome-proof {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-top: 32px;
        }
        .outcome-proof div {
            padding: 18px 0 0;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            position: relative;
        }
        .outcome-proof div::before {
            content: "";
            position: absolute;
            top: -1px;
            left: 0;
            width: 40px;
            height: 1px;
            background: linear-gradient(90deg, #06b6d4, transparent);
        }
        .outcome-proof span {
            display: block;
            color: #67e8f9;
            font-size: 28px;
            line-height: 1;
            font-weight: 900;
            letter-spacing: -0.02em;
        }
        .outcome-proof small {
            display: block;
            margin-top: 8px;
            color: #e2e8f0;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.4;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .outcome-list {
            display: flex;
            flex-direction: column;
            background: transparent;
            border-top: 1px solid var(--line);
        }
        .outcome-card {
            display: flex;
            gap: 22px;
            align-items: start;
            padding: 28px 20px;
            margin: 0 -20px;
            border-radius: 16px;
            border-bottom: 1px solid var(--line);
            transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
            position: relative;
        }
        .outcome-card::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.04), rgba(6, 182, 212, 0.01));
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }
        .outcome-card:hover::before {
            opacity: 1;
        }
        .outcome-card:last-child {
            border-bottom: 0;
        }
        .outcome-card:hover {
            transform: translateX(4px);
        }
        .outcome-number {
            display: inline-flex;
            width: 48px;
            height: 48px;
            flex: 0 0 48px;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: linear-gradient(135deg, #ecfeff 0%, #e0f2fe 100%);
            color: #0891b2;
            font-size: 15px;
            font-weight: 900;
            border: 1px solid rgba(6, 182, 212, 0.2);
            box-shadow: 0 4px 12px rgba(6, 182, 212, 0.1);
            transition: all 0.3s ease;
        }
        .outcome-card:hover .outcome-number {
            background: linear-gradient(135deg, #0891b2, #06b6d4);
            color: white;
            box-shadow: 0 6px 20px rgba(6, 182, 212, 0.3);
            transform: scale(1.05);
        }
        .outcome-card h3 {
            font-size: 17px;
            font-weight: 800;
            color: var(--navy-2);
            transition: color 0.3s ease;
        }
        .outcome-card:hover h3 {
            color: #0891b2;
        }
        .outcome-card p {
            margin-top: 10px;
            color: var(--slate);
            font-size: 14px;
            line-height: 1.7;
        }
        .outcome-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 18px;
            margin-top: 36px;
            padding-top: 28px;
            border-top: 1px solid var(--line);
        }
        .outcome-included {
            color: #94a3b8;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .outcome-included::before {
            content: "";
            width: 8px;
            height: 8px;
            background: linear-gradient(135deg, #10b981, #06b6d4);
            border-radius: 50%;
            flex-shrink: 0;
        }
        .outcome-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #0891b2;
            font-size: 14px;
            font-weight: 800;
            padding: 12px 24px;
            border-radius: 12px;
            background: rgba(6, 182, 212, 0.08);
            border: 1px solid rgba(6, 182, 212, 0.2);
            transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
        }
        .outcome-link:hover {
            background: linear-gradient(135deg, #0891b2, #06b6d4);
            color: white;
            border-color: transparent;
            box-shadow: 0 8px 24px rgba(6, 182, 212, 0.3);
            transform: translateY(-2px);
        }
        .outcome-link svg {
            width: 17px;
            height: 17px;
            transition: transform 0.3s ease;
        }
        .outcome-link:hover svg {
            transform: translateX(4px);
        }
        .outcome-link:hover svg {
            transform: translateX(3px);
        }

        /* ─── CTA GATEWAY ─── */
        .cta-gateway {
            position: relative;
            padding: 86px 0 96px;
            text-align: center;
            background:
                linear-gradient(135deg, rgba(2, 6, 23, 0.97), rgba(15, 23, 42, 0.94)),
                url("{{ asset('assets/images/audio-analysis-login-bg.png') }}") center / cover no-repeat;
            overflow: hidden;
        }

        /* Animated gradient orb */
        .cta-orb {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: min(760px, 90vw);
            height: 260px;
            border-radius: 999px;
            background: radial-gradient(ellipse, rgba(6, 182, 212, 0.13) 0%, transparent 68%);
            filter: blur(46px);
            pointer-events: none;
        }
        @keyframes orbPulse {
            0%, 100% { opacity: 0.72; }
            50% { opacity: 0.9; }
        }

        /* Glowing ring around orb */
        .cta-ring {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: min(680px, calc(100% - 48px));
            height: 1px;
            border: 0;
            border-top: 1px solid rgba(103, 232, 249, 0.18);
            pointer-events: none;
        }
        .cta-ring::before {
            display: none;
        }
        @keyframes ringRotate {
            from { transform: translate(-50%, -50%) rotate(0deg); }
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }

        /* Floating dots */
        .cta-dots {
            display: none;
        }
        .cta-dot {
            position: absolute;
            width: 3px;
            height: 3px;
            border-radius: 50%;
            background: rgba(139, 92, 246, 0.5);
            animation: dotFloat 8s ease-in-out infinite;
        }
        .cta-dot:nth-child(1) { top: 20%; left: 15%; animation-delay: 0s; animation-duration: 7s; }
        .cta-dot:nth-child(2) { top: 35%; left: 80%; animation-delay: 1s; animation-duration: 9s; background: rgba(6, 182, 212, 0.5); }
        .cta-dot:nth-child(3) { top: 70%; left: 25%; animation-delay: 2s; animation-duration: 6s; }
        .cta-dot:nth-child(4) { top: 15%; left: 65%; animation-delay: 0.5s; animation-duration: 8s; background: rgba(250, 204, 21, 0.4); }
        .cta-dot:nth-child(5) { top: 80%; left: 70%; animation-delay: 3s; animation-duration: 10s; background: rgba(6, 182, 212, 0.4); }
        .cta-dot:nth-child(6) { top: 50%; left: 10%; animation-delay: 1.5s; animation-duration: 7.5s; }
        .cta-dot:nth-child(7) { top: 60%; left: 88%; animation-delay: 2.5s; animation-duration: 6.5s; background: rgba(250, 204, 21, 0.3); }
        .cta-dot:nth-child(8) { top: 40%; left: 45%; animation-delay: 4s; animation-duration: 11s; background: rgba(139, 92, 246, 0.3); }
        @keyframes dotFloat {
            0%, 100% { transform: translateY(0) translateX(0); opacity: 0.4; }
            25% { transform: translateY(-20px) translateX(10px); opacity: 0.8; }
            50% { transform: translateY(-8px) translateX(-12px); opacity: 0.5; }
            75% { transform: translateY(-25px) translateX(6px); opacity: 0.9; }
        }

        .cta-gateway .container { position: relative; z-index: 2; }
        .cta-gateway h2 {
            font-size: clamp(30px, 4vw, 44px);
            line-height: 1.1;
            font-weight: 800;
            color: var(--white);
            letter-spacing: 0;
            max-width: 680px;
            margin: 0 auto;
        }
        .cta-gateway h2 .cta-gradient {
            background: linear-gradient(135deg, #67e8f9, #38bdf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .cta-gateway p {
            max-width: 520px;
            margin: 18px auto 0;
            color: #cbd5e1;
            font-size: 16px;
            line-height: 1.7;
        }
        .cta-gateway-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-top: 30px;
        }
        .cta-btn-glow {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 48px;
            padding: 0 28px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 800;
            color: #fff;
            background: linear-gradient(135deg, #0891b2, #2563eb);
            border: none;
            cursor: pointer;
            position: relative;
            transition: transform 0.25s, box-shadow 0.25s;
            box-shadow: 0 14px 30px rgba(6, 182, 212, 0.2);
        }
        .cta-btn-glow:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 36px rgba(6, 182, 212, 0.26);
        }
        .cta-btn-glow::before {
            display: none;
        }
        .cta-btn-glow:hover::before {
            opacity: 1;
        }
        .cta-secondary-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 48px;
            padding: 0 22px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            color: #cbd5e1;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.03);
            transition: border-color 0.25s, color 0.25s, background 0.25s;
        }
        .cta-secondary-link:hover {
            color: #fff;
            border-color: rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.06);
        }
        .cta-trust {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 22px;
            margin-top: 34px;
            color: #94a3b8;
            font-size: 12px;
            font-weight: 600;
        }
        .cta-trust-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .cta-trust-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 8px rgba(34, 197, 94, 0.5);
        }

        [data-reveal] {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        [data-reveal].revealed {
            opacity: 1;
            transform: translateY(0);
        }
        [data-reveal="left"] { transform: translateX(-28px); }
        [data-reveal="left"].revealed { transform: translateX(0); }
        [data-reveal="scale"] { transform: scale(0.96); }
        [data-reveal="scale"].revealed { transform: scale(1); }
        [data-reveal].revealed .flow-card,
        [data-reveal].revealed .stat-card,
        [data-reveal].revealed .summary-card,
        [data-reveal].revealed .output-card,
        [data-reveal].revealed .outcome-card,
        [data-reveal].revealed .audience-card {
            opacity: 0;
            animation: heroRevealUp 0.5s ease forwards;
        }

        /* ─── FOOTER ─── */
        footer {
            border-top: 1px solid var(--line);
            padding: 26px 0;
            color: var(--slate-2);
            font-size: 13px;
            font-weight: 650;
        }
        .footer-inner {
            display: flex;
            justify-content: space-between;
            gap: 18px;
        }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 980px) {
            .nav-links a:not(.login-link) {
                display: none;
            }
            .hero-grid,
            .inside-grid {
                grid-template-columns: 1fr;
            }
            .inside-grid {
                padding: 14px;
            }
            .dashboard-shell {
                max-width: 720px;
            }
            .floating-badge {
                display: none;
            }
            .flow-grid,
            .portal-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 16px;
            }
            .outcome-panel {
                grid-template-columns: 1fr;
                gap: 32px;
            }
            .flow-grid {
                gap: 32px;
            }
            .flow-grid::before,
            .flow-grid::after {
                display: none;
            }
            .output-flow {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            .output-flow .flow-arrow {
                flex-direction: row;
                padding: 4px 0;
            }
            .output-flow .flow-arrow svg {
                transform: rotate(90deg);
                width: 24px;
                height: 24px;
            }
            .output-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            .flow-arrow {
                display: none;
            }
        }
        @media (max-width: 680px) {
            .container {
                width: min(100% - 28px, 1180px);
            }
            .nav-inner {
                min-height: 66px;
            }
            .brand {
                font-size: 18px;
            }
            .hero {
                padding: 108px 0 58px;
            }
            .hero-orb { display: none; }
            h1 {
                font-size: 38px;
            }
            .hero-copy,
            .section-heading p,
            .cta-gateway h2 {
                font-size: 28px;
            }
            .cta-gateway p {
                font-size: 15px;
            }
            .cta-gateway-actions {
                flex-direction: column;
                align-items: stretch;
            }
            .cta-btn-glow,
            .cta-secondary-link {
                width: 100%;
                justify-content: center;
            }
            .cta-trust {
                flex-direction: column;
                gap: 12px;
            }
            .hero-actions {
                flex-direction: column;
                align-items: stretch;
            }
            .primary-btn,
            .secondary-btn {
                width: 100%;
            }
            .login-link {
                width: auto;
            }
            .dashboard-shell {
                padding: 10px;
                border-radius: 20px;
            }
            .dashboard {
                padding: 16px;
                border-radius: 16px;
            }
            .dashboard-head,
            .footer-inner {
                flex-direction: column;
                align-items: flex-start;
            }
            .metric-grid,
            .report-grid,
            .flow-grid,
            .output-grid,
            .output-flow,
            .portal-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            .outcome-summary {
                min-height: auto;
                border-radius: 22px;
            }
            .outcome-image-shape {
                height: 160px;
            }
            .outcome-bracket { display: none; }
            .outcome-mesh { height: 80px; }
            .outcome-summary-text {
                padding: 24px 26px 32px;
            }
            .outcome-summary-text h3 {
                font-size: 24px;
            }
            .outcome-summary-text h3 .yellow-highlight::after {
                display: none;
            }
            .outcome-card {
                gap: 12px;
                padding: 20px 14px;
                margin: 0;
            }
            .outcome-number {
                width: 42px;
                height: 42px;
                flex: 0 0 42px;
                border-radius: 12px;
                font-size: 14px;
            }
            .outcome-proof span {
                font-size: 24px;
            }
            .outcome-proof small {
                font-size: 10px;
            }
            .inside-grid {
                grid-template-columns: 1fr;
                padding: 0;
                border: 0;
                background: transparent;
                box-shadow: none;
            }
            .inside-grid::before {
                display: none;
            }
            .summary-card {
                grid-row: auto;
                min-height: auto;
            }
        }
    </style>
</head>
<body>
    <header class="nav">
        <div class="container nav-inner">
            <a class="brand" href="{{ url('/') }}" aria-label="Evalia home">
                <img src="{{ asset('assets/images/logo_white.png') }}" alt="Evalia" class="brand-logo">
                <span class="nav-pill">audio qa</span>
            </a>
            <nav class="nav-links" aria-label="Primary navigation">
                <a href="#how">How it works</a>
                <a href="#report">Report</a>
                <a href="#teams">Teams</a>
                <a class="login-link" href="{{ url('/login') }}">Login</a>
            </nav>
        </div>
    </header>

    <main>
        <!-- HERO -->
        <section class="hero">
            <!-- Background layers -->
            <div class="hero-noise"></div>
            <div class="hero-grid-bg"></div>
            <div class="hero-orb hero-orb--1"></div>
            <div class="hero-orb hero-orb--2"></div>
            <div class="hero-orb hero-orb--3"></div>
            <div class="hero-orb hero-orb--4"></div>

            <div class="container hero-grid">
                <div class="hero-content">
                    <p class="eyebrow">Evalia</p>
                    <h1>Turn every customer call into actionable <span class="highlight">quality insights.</span></h1>
                    <p class="hero-copy">
                        Evalia analyzes calls, scores quality, flags risk, and shows managers exactly what to coach next.
                    </p>
                    <div class="hero-actions">
                        <a class="primary-btn" href="{{ url('/login') }}">Login</a>
                        <a class="secondary-btn" href="#report">View QA Report</a>
                    </div>
                </div>

                <div class="dashboard-wrapper">
                    <div class="dashboard-shell" aria-label="Evalia QA report preview">
                        <div class="dashboard">
                            <div class="dashboard-head">
                                <div>
                                    <h2>Evalia QA Report</h2>
                                    <p>Support call - Billing dispute - 08:42</p>
                                </div>
                                <span class="status">AI analysis complete</span>
                            </div>

                            <div class="metric-grid">
                                <div class="metric">
                                    <span>QA Score</span>
                                    <strong data-count="92">92</strong>
                                </div>
                                <div class="metric">
                                    <span>Compliance</span>
                                    <strong data-count="88">88</strong>
                                </div>
                                <div class="metric">
                                    <span>Sentiment</span>
                                    <strong data-count="24">+24%</strong>
                                </div>
                            </div>

                            <div class="report-grid">
                                <div class="transcript">
                                    <div class="transcript-title">
                                        <span>Annotated transcript</span>
                                        <span>File</span>
                                    </div>
                                    <div class="line">
                                        <span class="speaker">Agent</span>
                                        <p>I can help with the refund request.</p>
                                    </div>
                                    <div class="line warning">
                                        <span class="speaker">Customer</span>
                                        <p>I was charged twice and need this fixed today.</p>
                                    </div>
                                </div>

                                <div class="side-stack">
                                    <div class="risk-card">
                                        <h3>Risk detected</h3>
                                        <p>Refund policy disclosure was missed at 04:18.</p>
                                    </div>
                                    <div class="coach-card">
                                        <h3>Coaching recommendation</h3>
                                        <p>Practice policy explanation before offering resolution options.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating badges -->
                    <div class="floating-badge floating-badge--score">
                        <span class="badge-dot badge-dot--green"></span>
                        92 Score
                    </div>
                    <div class="floating-badge floating-badge--risk">
                        <span class="badge-dot badge-dot--rose"></span>
                        1 Risk
                    </div>
                    <div class="floating-badge floating-badge--coach">
                        <span class="badge-dot badge-dot--cyan"></span>
                        Coaching
                    </div>
                </div>
            </div>
        </section>

        <!-- HOW IT WORKS -->
        <section class="section soft" id="how">
            <div class="container">
                <div class="section-heading center" data-reveal>
                    <p class="section-kicker">How it works</p>
                    <h2>From call to coaching in four steps</h2>
                </div>

                <div class="how-split" data-reveal>
                    <div class="how-visual">
                        <div class="how-visual-inner">
                            <img src="{{ asset('assets/images/ai-audio-waveform.png') }}" alt="AI Audio Analysis Visualization" class="how-hero-img" loading="lazy">
                            <div class="how-float-card how-float-card--top">
                                <span class="how-float-dot"></span>
                                Recording active
                            </div>
                            <div class="how-float-card how-float-card--bottom">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                Quality score: 92%
                            </div>
                        </div>
                    </div>

                    <div class="how-steps">
                        <article class="how-step">
                            <div class="how-step-left">
                                <div class="how-step-circle" style="background: linear-gradient(135deg, #06b6d4, #22d3ee);">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" x2="12" y1="19" y2="22"/></svg>
                                </div>
                                <span class="how-step-num">1</span>
                            </div>
                            <div class="how-step-text">
                                <h3>Capture</h3>
                                <p>Record calls directly from your existing phone or contact-center workflow.</p>
                            </div>
                        </article>

                        <article class="how-step">
                            <div class="how-step-left">
                                <div class="how-step-circle" style="background: linear-gradient(135deg, #7c3aed, #a78bfa);">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M8 10h.01"/><path d="M12 10h.01"/><path d="M16 10h.01"/></svg>
                                </div>
                                <span class="how-step-num">2</span>
                            </div>
                            <div class="how-step-text">
                                <h3>Analyze</h3>
                                <p>AI interprets conversation flow, sentiment, and compliance in real time.</p>
                            </div>
                        </article>

                        <article class="how-step">
                            <div class="how-step-left">
                                <div class="how-step-circle" style="background: linear-gradient(135deg, #059669, #34d399);">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                </div>
                                <span class="how-step-num">3</span>
                            </div>
                            <div class="how-step-text">
                                <h3>Score</h3>
                                <p>Every call gets a consistent quality and compliance score you can trust.</p>
                            </div>
                        </article>

                        <article class="how-step">
                            <div class="how-step-left">
                                <div class="how-step-circle" style="background: linear-gradient(135deg, #d97706, #fbbf24);">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                                </div>
                                <span class="how-step-num">4</span>
                            </div>
                            <div class="how-step-text">
                                <h3>Coach</h3>
                                <p>Managers receive targeted coaching actions anchored to real call moments.</p>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <!-- INSIDE THE REPORT -->
        <section class="section" id="report">
            <div class="container">
                <div class="section-heading" data-reveal="left">
                    <p class="section-kicker">Inside the report</p>
                    <h2>The page managers actually use after every call.</h2>
                </div>

                <div class="inside-grid" data-reveal>
                    <article class="summary-card">
                        <p class="label">AI Summary</p>
                        <h3>Customer asked for a refund after a duplicate charge.</h3>
                        <p>
                            The agent showed empathy and resolved the issue, but missed one required policy disclosure before confirming the next step.
                        </p>
                        <div class="recommendation">
                            <b>Recommended coaching</b>
                            <span>Practice the refund disclosure script before offering resolution options.</span>
                        </div>
                    </article>

                    <article class="stat-card">
                        <div class="stat-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 15.1 8.3 22 9.3 17 14.2 18.2 21 12 17.8 5.8 21 7 14.2 2 9.3 8.9 8.3 12 2Z"/></svg>
                        </div>
                        <p class="label">QA Score</p>
                        <strong>92/100</strong>
                        <p>Strong call handling</p>
                    </article>
                    <article class="stat-card">
                        <div class="stat-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg>
                        </div>
                        <p class="label">Compliance</p>
                        <strong>1 issue</strong>
                        <p>Disclosure missed</p>
                    </article>
                    <article class="stat-card">
                        <div class="stat-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17 9 11l4 4 8-8"/><path d="M14 7h7v7"/></svg>
                        </div>
                        <p class="label">Sentiment</p>
                        <strong>Improved</strong>
                        <p>Customer ended positive</p>
                    </article>
                    <article class="stat-card">
                        <div class="stat-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                        </div>
                        <p class="label">Key moment</p>
                        <strong>04:18</strong>
                        <p>Review this timestamp</p>
                    </article>
                </div>
            </div>
        </section>

        <!-- DARK OUTPUT -->
        <section class="section dark">
            <div class="dark-wrap">
                <div class="container">
                    <div class="dark-heading" data-reveal>
                        <p class="section-kicker">End-to-end pipeline</p>
                        <h2>From conversation to coaching in seconds.</h2>
                    </div>
                    <div class="output-flow" data-reveal="scale">
                        <article class="output-card">
                            <div class="output-step">
                                <span class="output-step-num">01</span>
                                <span class="output-step-label">Input</span>
                            </div>
                            <div class="icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" x2="12" y1="19" y2="22"/></svg>
                            </div>
                            <h3>Customer call</h3>
                            <p>Evalia starts with real customer conversations, recordings, or transcripts.</p>
                        </article>
                        <div class="flow-arrow">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            <span class="flow-arrow-label">Process</span>
                        </div>
                        <article class="output-card">
                            <div class="output-step">
                                <span class="output-step-num">02</span>
                                <span class="output-step-label">AI Analysis</span>
                            </div>
                            <div class="icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4"/><path d="m16.2 7.8 2.9-2.9"/><path d="M18 12h4"/><path d="m16.2 16.2 2.9 2.9"/><path d="M12 18v4"/><path d="m4.9 19.1 2.9-2.9"/><path d="M2 12h4"/><path d="m4.9 4.9 2.9 2.9"/><circle cx="12" cy="12" r="4"/></svg>
                            </div>
                            <h3>Analysis + understanding</h3>
                            <p>The platform finds intent, risk, sentiment, objections, and process gaps.</p>
                        </article>
                        <div class="flow-arrow">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            <span class="flow-arrow-label">Deliver</span>
                        </div>
                        <article class="output-card">
                            <div class="output-step">
                                <span class="output-step-num">03</span>
                                <span class="output-step-label">Output</span>
                            </div>
                            <div class="icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>
                            </div>
                            <h3>QA score + coaching</h3>
                            <p>Managers receive clear scores, flagged moments, and coaching recommendations.</p>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <!-- OUTCOMES -->
        <section class="section">
            <div class="container">
                <div class="solo-heading" data-reveal>
                    <p class="section-kicker" style="display:inline-flex;align-items:center;gap:8px;padding:6px 14px;background:rgba(6,182,212,0.08);border:1px solid rgba(6,182,212,0.15);border-radius:999px;color:#0891b2;font-size:12px;font-weight:700;letter-spacing:0.06em;text-transform:uppercase;margin-bottom:18px;">Outcomes</p>
                    <h2>What your team gets</h2>
                    <p style="margin-top:14px;color:var(--slate);font-size:16px;line-height:1.7;max-width:520px;margin-left:auto;margin-right:auto;">Every call becomes structured evidence for faster reviews, better coaching, and earlier risk detection.</p>
                </div>
                <div class="outcome-panel" data-reveal>
                    <div class="outcome-summary">
                        <div class="outcome-image-shape">
                            <img src="{{ asset('assets/images/qa-analytics-outcomes.png') }}" alt="Evalia QA Performance Outcomes" loading="lazy">
                            <div class="outcome-scan"></div>
                            <div class="outcome-bracket outcome-bracket--tl"></div>
                            <div class="outcome-bracket outcome-bracket--tr"></div>
                            <div class="outcome-bracket outcome-bracket--bl"></div>
                            <div class="outcome-bracket outcome-bracket--br"></div>
                            <div class="mesh-glow"></div>
                            <div class="outcome-mesh">
                                <canvas id="meshCanvas"></canvas>
                            </div>
                        </div>
                        <div class="outcome-summary-text">
                            <p class="section-kicker">Operational impact</p>
                            <h3>Move QA from <span class="yellow-highlight">random sampling</span> to focused decisions.</h3>
                            <p>Evalia turns every call into structured evidence, so managers can <span class="yellow-highlight">prioritize review time</span>, coaching, and risk follow-up with confidence.</p>
                            <div class="outcome-proof">
                                <div>
                                    <span>100%</span>
                                    <small>Call visibility</small>
                                </div>
                                <div>
                                    <span>3x</span>
                                    <small>Faster review flow</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="outcome-list">
                        <article class="outcome-card">
                            <p class="outcome-number">01</p>
                            <div>
                                <h3>Review faster</h3>
                                <p>QA teams go straight to the calls and moments that matter instead of manually scanning every recording.</p>
                            </div>
                        </article>
                        <article class="outcome-card">
                            <p class="outcome-number">02</p>
                            <div>
                                <h3>Coach better</h3>
                                <p>Managers see exactly what each agent should improve, backed by examples from the call.</p>
                            </div>
                        </article>
                        <article class="outcome-card">
                            <p class="outcome-number">03</p>
                            <div>
                                <h3>Find problems earlier</h3>
                                <p>Risk, sentiment drops, and policy issues surface before they become recurring team patterns.</p>
                            </div>
                        </article>
                        <div class="outcome-actions">
                            <span class="outcome-included">Included with every Evalia workspace</span>
                            <a class="outcome-link" href="{{ url('/login') }}">
                                Login
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- TEAMS -->
        <section class="teams-section" id="teams">
            <div class="container">
                <div class="section-heading center" data-reveal>
                    <p class="section-kicker">Who it's for</p>
                    <h2>Built for teams that own call quality.</h2>
                    <p>Every role gets the visibility they need — from the floor to the frontline.</p>
                </div>

                <div class="portal-grid" data-reveal>
                    <!-- Portal 1: QA Managers -->
                    <div class="portal portal--vault">
                        <div class="portal-bg"></div>
                        <div class="portal-content">
                            <div class="portal-icon-wrap">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg>
                            </div>
                            <span class="portal-tag">Quality Assurance</span>
                            <h3>QA Managers</h3>
                            <p>Monitor call quality without manually reviewing every recording. Spot trends and flag risks across the entire team.</p>
                            <ul class="portal-features">
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                    100% call coverage with AI scoring
                                </li>
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                    Real-time compliance monitoring
                                </li>
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                    Risk flagging and escalation alerts
                                </li>
                            </ul>
                            <div class="portal-stats-row">
                                <div class="portal-mini-stat">
                                    <strong>94%</strong>
                                    <small>Coverage</small>
                                </div>
                                <div class="portal-mini-stat">
                                    <strong>3x</strong>
                                    <small>Faster audits</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Portal 2: L&D Teams -->
                    <div class="portal portal--grove">
                        <div class="portal-bg"></div>
                        <div class="portal-content">
                            <div class="portal-icon-wrap">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            </div>
                            <span class="portal-tag">Learning & Development</span>
                            <h3>L&D Teams</h3>
                            <p>Turn real conversations into focused coaching opportunities. Build training around what actually happens on calls.</p>
                            <ul class="portal-features">
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                    AI-generated coaching recommendations
                                </li>
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                    Agent-level skill gap analysis
                                </li>
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                    Training plans from real call moments
                                </li>
                            </ul>
                            <div class="portal-stats-row">
                                <div class="portal-mini-stat">
                                    <strong>87%</strong>
                                    <small>Adoption</small>
                                </div>
                                <div class="portal-mini-stat">
                                    <strong>2.5x</strong>
                                    <small>Faster onboarding</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Portal 3: CX Leaders -->
                    <div class="portal portal--tide">
                        <div class="portal-bg"></div>
                        <div class="portal-content">
                            <div class="portal-icon-wrap">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
                            </div>
                            <span class="portal-tag">Customer Experience</span>
                            <h3>CX Leaders</h3>
                            <p>Understand customer experience patterns across agents and teams. Make data-driven decisions at scale.</p>
                            <ul class="portal-features">
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                    Sentiment tracking per customer journey
                                </li>
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                    Cross-team experience benchmarking
                                </li>
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                    Trend detection and churn prediction
                                </li>
                            </ul>
                            <div class="portal-stats-row">
                                <div class="portal-mini-stat">
                                    <strong>91%</strong>
                                    <small>Insight rate</small>
                                </div>
                                <div class="portal-mini-stat">
                                    <strong>40%</strong>
                                    <small>Less churn</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA GATEWAY -->
        <section class="cta-gateway">
            <div class="cta-orb"></div>
            <div class="cta-ring"></div>
            <div class="cta-dots">
                <span class="cta-dot"></span>
                <span class="cta-dot"></span>
                <span class="cta-dot"></span>
                <span class="cta-dot"></span>
                <span class="cta-dot"></span>
                <span class="cta-dot"></span>
                <span class="cta-dot"></span>
                <span class="cta-dot"></span>
            </div>
            <div class="container">
                <h2>Step into your <span class="cta-gradient">Evalia workspace</span></h2>
                <p>Review calls, manage evaluations, and track quality performance — all in one place.</p>
                <div class="cta-gateway-actions">
                    <a class="cta-btn-glow" href="{{ url('/login') }}">Login to Evalia</a>
                    <a class="cta-secondary-link" href="#report">
                        View sample report
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                </div>
                <div class="cta-trust">
                    <span class="cta-trust-item">
                        <span class="cta-trust-dot"></span>
                        AI-powered analysis
                    </span>
                    <span class="cta-trust-item">
                        <span class="cta-trust-dot"></span>
                        No setup required
                    </span>
                    <span class="cta-trust-item">
                        <span class="cta-trust-dot"></span>
                        Works with your existing calls
                    </span>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container footer-inner">
            <span>Evalia</span>
            <span>AI audio QA for call quality, compliance, and coaching</span>
        </div>
    </footer>
    <script>
        const revealItems = document.querySelectorAll('[data-reveal]');

        if ('IntersectionObserver' in window) {
            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        revealObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.18 });

            revealItems.forEach((item) => revealObserver.observe(item));
        } else {
            revealItems.forEach((item) => item.classList.add('revealed'));
        }

        /* ─── DATA WAVE ANIMATION ─── */
        (function () {
            const canvas = document.getElementById('meshCanvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            let w, h, time = 0;

            const COLS = 30;
            const ROWS = 6;
            const speed = 0.018;

            function resize() {
                const rect = canvas.parentElement.getBoundingClientRect();
                const dpr = window.devicePixelRatio || 1;
                w = rect.width;
                h = rect.height;
                canvas.width = w * dpr;
                canvas.height = h * dpr;
                canvas.style.width = w + 'px';
                canvas.style.height = h + 'px';
                ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
            }

            function getY(col, row, t) {
                const xRatio = col / COLS;
                const yRatio = row / ROWS;
                var wave = Math.sin(xRatio * Math.PI * 2.5 + t) * (14 + row * 2.5);
                wave += Math.cos(xRatio * Math.PI * 1.8 + t * 1.4) * (6 + row * 1.2);
                wave += Math.sin(xRatio * Math.PI * 4 + t * 2.1) * 3;
                var baseY = h - row * (h / (ROWS + 1)) - 8;
                return baseY - wave * (1 - yRatio * 0.4);
            }

            function draw() {
                ctx.clearRect(0, 0, w, h);

                /* Horizontal wave lines */
                for (var row = 0; row < ROWS; row++) {
                    var alpha = 0.32 - row * 0.04;
                    ctx.strokeStyle = 'rgba(34,211,238,' + alpha + ')';
                    ctx.lineWidth = 1;
                    ctx.beginPath();
                    for (var col = 0; col <= COLS; col++) {
                        var x = (col / COLS) * w;
                        var y = getY(col, row, time);
                        if (col === 0) ctx.moveTo(x, y);
                        else ctx.lineTo(x, y);
                    }
                    ctx.stroke();
                }

                /* Vertical connecting lines */
                for (var col = 0; col <= COLS; col++) {
                    var distFromCenter = Math.abs(col - COLS / 2) / (COLS / 2);
                    var alpha2 = 0.14 - distFromCenter * 0.08;
                    ctx.strokeStyle = 'rgba(34,211,238,' + alpha2 + ')';
                    ctx.lineWidth = 0.5;
                    ctx.beginPath();
                    for (var row = 0; row < ROWS; row++) {
                        var x = (col / COLS) * w;
                        var y = getY(col, row, time);
                        if (row === 0) ctx.moveTo(x, y);
                        else ctx.lineTo(x, y);
                    }
                    ctx.stroke();
                }

                /* Node dots at intersections */
                for (var row = 0; row < ROWS; row++) {
                    for (var col = 0; col <= COLS; col++) {
                        var x = (col / COLS) * w;
                        var y = getY(col, row, time);
                        var dotAlpha = 0.4 - row * 0.055;
                        var dotR = 1.3 - row * 0.12;
                        ctx.fillStyle = 'rgba(103,232,249,' + dotAlpha + ')';
                        ctx.beginPath();
                        ctx.arc(x, y, Math.max(dotR, 0.4), 0, Math.PI * 2);
                        ctx.fill();
                    }
                }

                time += speed;
                requestAnimationFrame(draw);
            }

            resize();
            draw();
            window.addEventListener('resize', resize);
        })();
    </script>
</body>
</html>
