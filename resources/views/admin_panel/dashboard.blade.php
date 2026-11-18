@extends('admin_panel.layout.app')

@section('title', 'ProWave Technologies - Enterprise FMGC SaaS Dashboard')

@section('content')
<!-- Font Awesome 6 & Google Fonts -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">

<style>
    /* =========================================================
       PROWAVE TECHNOLOGIES - ULTRA PREMIUM 3D GLASS DASHBOARD
       (PIXEL-PERFECT REPLICA OF THE CLIENT'S DESIGN)
       ========================================================= */
    :root {
        --f-sans: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        --f-display: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif;
        --bg-canvas: #f1f5fa;
        --card-white: #ffffff;
        --text-dark: #0f172a;
        --text-sub: #475569;
        --text-muted: #64748b;

        --c-blue: #2563eb;
        --c-emerald: #10b981;
        --c-amber: #f59e0b;
        --c-orange: #f97316;
        --c-purple: #8b5cf6;
        --c-rose: #f43f5e;
    }

    .pw-master-canvas {
        font-family: var(--f-sans);
        background: linear-gradient(180deg, #edf3fa 0%, #f6f9fc 100%);
        min-height: calc(100vh - 75px);
        padding: 1.25rem 1.5rem 3rem;
        color: var(--text-dark);
        box-sizing: border-box;
        overflow-x: hidden;
        width: 100%;
    }

    .pw-master-canvas * {
        box-sizing: border-box;
    }

    /* Top Brand & Executive Control Card */
    .pw-top-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 1.5rem;
        padding: 14px 22px;
        background: #ffffff;
        border: 1px solid #eef2f7;
        border-radius: 20px;
        box-shadow: 0 4px 20px -4px rgba(15, 23, 42, 0.05);
    }

    .pw-brand-badge {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .pw-brand-logo-cube {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-family: var(--f-display);
        font-weight: 900;
        font-size: 1.3rem;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.35), inset 0 2px 2px rgba(255, 255, 255, 0.5);
        flex-shrink: 0;
    }

    .pw-brand-info h4 {
        font-family: var(--f-display);
        font-weight: 800;
        font-size: 1.35rem;
        margin: 0;
        color: #0f172a;
        letter-spacing: -0.3px;
    }

    .pw-brand-info p {
        margin: 0;
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
    }

    .pw-live-indicator {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(16, 185, 129, 0.12);
        color: #059669;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 30px;
        border: 1px solid rgba(16, 185, 129, 0.25);
        margin-left: 6px;
    }

    .pw-pulse-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 8px #10b981;
        animation: pwPulse 1.8s infinite;
    }

    /* Top Controls Row */
    .pw-top-controls {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        margin: -4px;
    }

    .pw-top-controls > * {
        margin: 4px 6px;
    }

    .pw-action-pill {
        height: 38px;
        padding: 0 16px;
        border-radius: 30px !important;
        font-size: 12.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #ffffff;
        color: #1e293b !important;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        transition: all 0.2s ease;
        text-decoration: none !important;
        cursor: pointer;
        line-height: 1;
        white-space: nowrap;
    }

    .pw-action-pill:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .pw-role-pill {
        height: 38px;
        padding: 0 16px;
        border-radius: 30px !important;
        font-size: 11.5px;
        font-weight: 800;
        letter-spacing: 0.3px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(139, 92, 246, 0.12);
        color: #7c3aed !important;
        border: 1px solid rgba(139, 92, 246, 0.25);
        line-height: 1;
        white-space: nowrap;
    }

    .pw-refresh-pill {
        height: 38px;
        padding: 0 18px;
        border-radius: 30px !important;
        font-size: 12.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff !important;
        border: none;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        transition: all 0.2s ease;
        cursor: pointer;
        line-height: 1;
        white-space: nowrap;
    }

    .pw-refresh-pill:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.4);
        transform: translateY(-1px);
    }

    @keyframes pwPulse {
        0% { transform: scale(0.95); opacity: 1; }
        50% { transform: scale(1.15); opacity: 0.7; }
        100% { transform: scale(0.95); opacity: 1; }
    }

    /* ─── TIER 1: TOP 4 3D GLOSSY PILL / TABLET CARDS ──────────────────────── */
    .pw-kpi-4row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 1.5rem;
    }

    .pw-pill-card {
        border-radius: 20px;
        padding: 1.15rem 1.35rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        text-decoration: none;
        color: inherit;
        transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }

    .pw-pill-card:hover {
        transform: translateY(-4px);
        text-decoration: none;
        color: inherit;
    }

    /* Glass top highlight sheen */
    .pw-pill-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 40%;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.6) 0%, rgba(255, 255, 255, 0) 100%);
        border-radius: 20px 20px 0 0;
        pointer-events: none;
    }

    /* Card 1: Blue Glass (Total Sales) */
    .pw-pill-blue {
        background: linear-gradient(135deg, rgba(224, 242, 254, 0.95) 0%, rgba(186, 230, 253, 0.85) 100%);
        border: 2px solid rgba(147, 197, 253, 0.9);
        box-shadow: 0 10px 24px -4px rgba(37, 99, 235, 0.18), inset 0 2px 4px rgba(255, 255, 255, 0.9);
    }
    .pw-pill-blue:hover {
        box-shadow: 0 16px 32px -4px rgba(37, 99, 235, 0.3), inset 0 2px 4px rgba(255, 255, 255, 0.9);
    }

    /* Card 2: Green Glass (Total Purchases) */
    .pw-pill-green {
        background: linear-gradient(135deg, rgba(220, 252, 231, 0.95) 0%, rgba(187, 247, 208, 0.85) 100%);
        border: 2px solid rgba(134, 239, 172, 0.9);
        box-shadow: 0 10px 24px -4px rgba(16, 185, 129, 0.18), inset 0 2px 4px rgba(255, 255, 255, 0.9);
    }
    .pw-pill-green:hover {
        box-shadow: 0 16px 32px -4px rgba(16, 185, 129, 0.3), inset 0 2px 4px rgba(255, 255, 255, 0.9);
    }

    /* Card 3: Orange Glass (Sales Returns) */
    .pw-pill-orange {
        background: linear-gradient(135deg, rgba(254, 243, 199, 0.95) 0%, rgba(254, 215, 170, 0.85) 100%);
        border: 2px solid rgba(253, 186, 116, 0.9);
        box-shadow: 0 10px 24px -4px rgba(249, 115, 22, 0.18), inset 0 2px 4px rgba(255, 255, 255, 0.9);
    }
    .pw-pill-orange:hover {
        box-shadow: 0 16px 32px -4px rgba(249, 115, 22, 0.3), inset 0 2px 4px rgba(255, 255, 255, 0.9);
    }

    /* Card 4: Purple Glass (Net Profit) */
    .pw-pill-purple {
        background: linear-gradient(135deg, rgba(243, 232, 255, 0.95) 0%, rgba(233, 213, 255, 0.85) 100%);
        border: 2px solid rgba(216, 180, 254, 0.9);
        box-shadow: 0 10px 24px -4px rgba(168, 85, 247, 0.18), inset 0 2px 4px rgba(255, 255, 255, 0.9);
    }
    .pw-pill-purple:hover {
        box-shadow: 0 16px 32px -4px rgba(168, 85, 247, 0.3), inset 0 2px 4px rgba(255, 255, 255, 0.9);
    }

    /* ─── TIER 1.5: EXECUTIVE SECONDARY 3D METRICS BAR ──────────────────────── */
    .pw-exec-strip {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 12px;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 1200px) {
        .pw-exec-strip { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 768px) {
        .pw-exec-strip { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 480px) {
        .pw-exec-strip { grid-template-columns: 1fr; }
    }
    .pw-exec-item {
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(226, 232, 240, 0.85);
        border-radius: 16px;
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: inherit;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
        transition: all 0.25s ease;
    }
    .pw-exec-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
        text-decoration: none;
        color: inherit;
        background: #ffffff;
    }
    .pw-exec-icon {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
    }
    .pw-exec-icon-blue { background: #eff6ff; color: #2563eb; }
    .pw-exec-icon-purple { background: #f5f3ff; color: #8b5cf6; }
    .pw-exec-icon-emerald { background: #ecfdf5; color: #10b981; }
    .pw-exec-icon-cyan { background: #ecfeff; color: #06b6d4; }
    .pw-exec-icon-amber { background: #fffbeb; color: #f59e0b; }
    .pw-exec-icon-rose { background: #fff1f2; color: #f43f5e; }
    .pw-exec-lbl { font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.3px; }
    .pw-exec-val { font-size: 13.5px; font-weight: 800; color: #0f172a; font-family: var(--f-display); margin-top: 1px; }

    /* 3D Floating Icon Box on Left */
    .pw-3d-box {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        color: #ffffff;
        flex-shrink: 0;
        margin-right: 14px;
        position: relative;
        box-shadow: 0 8px 18px -2px rgba(0, 0, 0, 0.22), inset 0 2px 3px rgba(255, 255, 255, 0.6), inset 0 -2px 4px rgba(0, 0, 0, 0.2);
        transition: transform 0.25s ease;
    }

    .pw-pill-card:hover .pw-3d-box {
        transform: scale(1.08) rotate(3deg);
    }

    .pw-3d-blue { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
    .pw-3d-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .pw-3d-orange { background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); }
    .pw-3d-purple { background: linear-gradient(135deg, #a855f7 0%, #7c3aed 100%); }

    /* Card Text Content */
    .pw-pill-content {
        flex: 1;
        min-width: 0;
    }

    .pw-pill-title {
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 2px;
    }

    .pw-pill-amount {
        font-family: var(--f-display);
        font-size: 1.55rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.4px;
        line-height: 1.2;
    }

    .pw-pill-trend {
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        margin-top: 3px;
    }

    .pw-trend-green { color: #059669; }
    .pw-trend-red { color: #dc2626; }

    /* 3D Mini Bar Sparkline on Right */
    .pw-sparkline-bars {
        display: flex;
        align-items: flex-end;
        gap: 3.5px;
        height: 38px;
        padding-left: 10px;
        flex-shrink: 0;
    }

    .pw-spark-bar {
        width: 5px;
        border-radius: 3px;
        transition: height 0.3s ease;
    }

    .pw-spark-blue { background: #3b82f6; }
    .pw-spark-green { background: #10b981; }
    .pw-spark-orange { background: #f97316; }
    .pw-spark-purple { background: #a855f7; }

    /* ─── TIER 2: 3 WHITE GLASS CONTAINER CARDS ───────────────────────────── */
    .pw-grid-tier2 {
        display: grid;
        grid-template-columns: 4.6fr 3.4fr 4fr;
        gap: 16px;
        margin-bottom: 1.5rem;
    }

    .pw-white-card {
        background: #ffffff;
        border: 1px solid #eef2f7;
        border-radius: 20px;
        padding: 1.25rem 1.4rem;
        box-shadow: 0 8px 24px -4px rgba(15, 23, 42, 0.05), 0 2px 6px -1px rgba(0, 0, 0, 0.02);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
    }

    .pw-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }

    .pw-card-title-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pw-card-title-icon {
        font-size: 1.1rem;
    }

    .pw-card-title {
        font-family: var(--f-display);
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        letter-spacing: -0.2px;
    }

    /* Small Pill Dropdown Selector */
    .pw-pill-select {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 4px 10px;
        font-size: 11.5px;
        font-weight: 700;
        color: #475569;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        text-decoration: none;
    }

    .pw-pill-select:hover {
        background: #f1f5f9;
        color: #1e293b;
        text-decoration: none;
    }

    .pw-link-all {
        font-size: 12px;
        font-weight: 700;
        color: #2563eb;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .pw-link-all:hover {
        text-decoration: underline;
        color: #1d4ed8;
    }

    /* Chart 1: Legend Header */
    .pw-chart-legend {
        display: flex;
        align-items: center;
        gap: 14px;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .pw-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 4px;
    }

    .pw-dot-blue { background: #2563eb; }
    .pw-dot-purple { background: #8b5cf6; }
    .pw-dot-green { background: #10b981; }
    .pw-dot-orange { background: #f97316; }
    .pw-dot-red { background: #ef4444; }

    /* Floating Tooltip Pill (Mockup Accent from Screenshot) */
    .pw-floating-pill {
        position: absolute;
        top: 65px;
        right: 110px;
        background: #0f172a;
        color: #ffffff;
        font-size: 11px;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.25);
        pointer-events: none;
        z-index: 5;
    }

    /* Donut Chart with Center Text */
    .pw-donut-wrap {
        position: relative;
        width: 130px;
        height: 130px;
        min-width: 130px;
        min-height: 130px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .pw-donut-wrap canvas {
        display: block;
        max-width: 100%;
        max-height: 100%;
    }

    .pw-donut-center {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        pointer-events: none;
        z-index: 2;
        width: 85px;
    }

    .pw-donut-val {
        font-family: var(--f-display);
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }

    .pw-donut-lbl {
        font-size: 9.5px;
        font-weight: 700;
        color: #64748b;
        margin-top: 2px;
        white-space: nowrap;
    }

    /* Business Analytics Bottom 2 Pills */
    .pw-analytics-pills {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 14px;
    }

    .pw-sub-pill {
        padding: 8px 10px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pw-sub-pill-green {
        background: #ecfdf5;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .pw-sub-pill-purple {
        background: #f5f3ff;
        border: 1px solid rgba(139, 92, 246, 0.2);
    }

    .pw-sub-pill-icon {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        flex-shrink: 0;
    }

    .pw-sub-pill-lbl {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        line-height: 1.1;
    }

    .pw-sub-pill-val {
        font-family: var(--f-display);
        font-size: 12px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }

    /* Recent Activities Timeline */
    .pw-timeline-list {
        display: flex;
        flex-direction: column;
        gap: 11px;
    }

    .pw-timeline-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .pw-timeline-left {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .pw-timeline-node {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 12px;
        flex-shrink: 0;
    }

    .pw-node-green { background: #10b981; }
    .pw-node-blue { background: #2563eb; }
    .pw-node-amber { background: #f59e0b; }
    .pw-node-purple { background: #8b5cf6; }

    .pw-timeline-title {
        font-weight: 700;
        font-size: 12px;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pw-timeline-sub {
        font-size: 11px;
        color: #64748b;
        font-weight: 500;
    }

    .pw-tag-pill {
        padding: 3px 10px;
        border-radius: 14px;
        font-size: 11px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .pw-tag-green { background: #dcfce7; color: #15803d; }
    .pw-tag-blue { background: #dbeafe; color: #1d4ed8; }
    .pw-tag-amber { background: #fef3c7; color: #b45309; }
    .pw-tag-purple { background: #f3e8ff; color: #7e22ce; }

    /* ─── TIER 3: 4 WHITE GLASS CONTAINER CARDS ───────────────────────────── */
    .pw-grid-tier3 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    /* Top Selling Products Progress Bars */
    .pw-prod-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 12px;
    }

    .pw-prod-item:last-child {
        margin-bottom: 0;
    }

    .pw-prod-thumb {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }

    .pw-prod-info {
        flex: 1;
        min-width: 0;
    }

    .pw-prod-name {
        font-size: 12px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pw-prod-bar-bg {
        width: 100%;
        height: 5px;
        background: #f1f5f9;
        border-radius: 3px;
        overflow: hidden;
    }

    .pw-prod-bar-fill {
        height: 100%;
        border-radius: 3px;
    }

    .pw-prod-pcs {
        font-family: var(--f-display);
        font-size: 12px;
        font-weight: 800;
        color: #0f172a;
        white-space: nowrap;
        padding-left: 6px;
    }

    /* Financial Overview 3-Pill Bottom Summary */
    .pw-fin-pills {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 6px;
        margin-top: 10px;
    }

    .pw-fin-pill {
        padding: 6px 4px;
        border-radius: 10px;
        text-align: center;
    }

    .pw-fin-green { background: #ecfdf5; border: 1px solid #a7f3d0; }
    .pw-fin-red { background: #fef2f2; border: 1px solid #fecaca; }
    .pw-fin-purple { background: #f5f3ff; border: 1px solid #ddd6fe; }

    .pw-fin-val {
        font-family: var(--f-display);
        font-size: 11.5px;
        font-weight: 800;
        line-height: 1.1;
    }

    .pw-fin-green .pw-fin-val { color: #059669; }
    .pw-fin-red .pw-fin-val { color: #dc2626; }
    .pw-fin-purple .pw-fin-val { color: #7c3aed; }

    .pw-fin-lbl {
        font-size: 9.5px;
        font-weight: 700;
        color: #64748b;
        margin-top: 2px;
    }

    /* Upcoming Tasks Checklist */
    .pw-task-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .pw-task-row {
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .pw-task-checkbox {
        width: 17px;
        height: 17px;
        border-radius: 5px;
        border: 1.5px solid #cbd5e1;
        cursor: pointer;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .pw-task-text {
        font-size: 12px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
        margin-bottom: 2px;
    }

    .pw-task-tag {
        font-size: 10.5px;
        font-weight: 600;
        color: #64748b;
    }

    .pw-tag-urgent { color: #ef4444; font-weight: 700; }
    .pw-tag-medium { color: #f59e0b; font-weight: 700; }
    .pw-tag-low { color: #3b82f6; font-weight: 700; }

    /* ─── TIER 4: MULTI-BRANCH MATRIX & FMCG EXPIRY WATCHDOG ─────────────── */
    .pw-grid-tier4 {
        display: grid;
        grid-template-columns: 1.35fr 1fr;
        gap: 16px;
        margin-top: 1.5rem;
    }

    .pw-expiry-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 10.5px;
        font-weight: 800;
        letter-spacing: 0.3px;
        line-height: 1.2;
    }

    .pw-exp-critical {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
        animation: pulse-border 2s infinite;
    }

    .pw-exp-warning {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fcd34d;
    }

    .pw-exp-safe {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #86efac;
    }

    .pw-exp-expired {
        background: #450a0a;
        color: #fecaca;
        border: 1px solid #991b1b;
    }

    @keyframes pulse-border {
        0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
        50% { box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15); }
    }

    /* Multi-branch card items */
    .pw-branch-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 12px;
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }

    .pw-branch-card:hover {
        background: #ffffff;
        border-color: #3b82f6;
        transform: translateY(-2px);
        box-shadow: 0 8px 16px -4px rgba(59, 130, 246, 0.12);
    }

    .pw-branch-card.active {
        background: #eff6ff;
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
    }

    .pw-branch-badge-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
        flex-shrink: 0;
    }

    /* 3D Depth & Tilt Micro-Animation */
    .pw-pill-card, .pw-white-card {
        transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.25s ease;
    }

    .pw-pill-card:hover {
        transform: translateY(-5px) scale(1.01);
    }

    .pw-white-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 32px -4px rgba(15, 23, 42, 0.08), 0 4px 12px -2px rgba(0, 0, 0, 0.03);
    }

    /* Responsive Breakpoints */
    @media (max-width: 1280px) {
        .pw-kpi-4row { grid-template-columns: repeat(2, 1fr); }
        .pw-grid-tier2 { grid-template-columns: 1fr; }
        .pw-grid-tier3 { grid-template-columns: repeat(2, 1fr); }
        .pw-grid-tier4 { grid-template-columns: 1fr; }
    }

    @media (max-width: 768px) {
        .pw-master-canvas { padding: 1rem 0.75rem 2rem; }
        .pw-kpi-4row { grid-template-columns: 1fr; }
        .pw-grid-tier3 { grid-template-columns: 1fr; }
        .pw-grid-tier4 { grid-template-columns: 1fr; }
    }
</style>

<div class="pw-master-canvas">

    <!-- Top Brand & Executive Control Header -->
    <div class="pw-top-bar">
        <div class="pw-brand-badge">
            <div class="pw-brand-logo-cube">
                <span>PW</span>
            </div>
            <div class="pw-brand-info">
                <div class="d-flex align-items-center gap-2">
                    <h4>ProWave Technologies</h4>
                    <span class="pw-live-indicator"><span class="pw-pulse-dot"></span> LIVE SYSTEM</span>
                </div>
                <p>Complete business insights at your fingertips</p>
            </div>
        </div>

        <div class="pw-top-controls">
            @if($isSuperAdmin)
                <!-- Super Admin Branch Switcher Dropdown -->
                <div class="dropdown">
                    <button class="pw-action-pill dropdown-toggle" type="button" data-toggle="dropdown" data-bs-toggle="dropdown">
                        <i class="fas fa-building text-primary"></i>
                        <span>{{ $currentBranch ? $currentBranch->name : 'All Branches (Enterprise)' }}</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right shadow border-0" style="font-size: 12.5px; border-radius: 16px; min-width: 240px; padding: 8px;">
                        <div class="dropdown-header text-uppercase text-muted" style="font-size: 10px; font-weight: 800; letter-spacing: 0.5px;">Switch Branch Scope</div>
                        <a class="dropdown-item rounded-3 py-2 d-flex align-items-center justify-content-between {{ empty($selectedBranchId) || $selectedBranchId === 'all' ? 'fw-bold text-primary active' : '' }}" 
                           href="{{ route('home', array_merge(request()->query(), ['branch_id' => 'all'])) }}">
                            <span><i class="fas fa-globe me-2 text-primary"></i> All Branches</span>
                            @if(empty($selectedBranchId) || $selectedBranchId === 'all')
                                <i class="fas fa-check text-primary"></i>
                            @endif
                        </a>
                        <div class="dropdown-divider my-1"></div>
                        @foreach($allBranches as $b)
                        <a class="dropdown-item rounded-3 py-2 d-flex align-items-center justify-content-between {{ (string)$selectedBranchId === (string)$b->id ? 'fw-bold text-primary active' : '' }}" 
                           href="{{ route('home', array_merge(request()->query(), ['branch_id' => $b->id])) }}">
                            <span><i class="fas fa-store me-2 text-muted"></i> {{ $b->name }}</span>
                            @if((string)$selectedBranchId === (string)$b->id)
                                <i class="fas fa-check text-primary"></i>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>

                <div class="pw-role-pill d-none d-md-inline-flex">
                    <i class="fas fa-crown text-warning"></i>
                    <span>Super Admin</span>
                </div>
            @else
                <!-- Branch Admin Locked Access Badge -->
                <div class="pw-action-pill" style="cursor: default; background: #f8fafc;">
                    <i class="fas fa-building text-primary"></i>
                    <span>{{ $currentBranch ? $currentBranch->name : (auth()->user()->branch ? auth()->user()->branch->name : 'Branch Office') }}</span>
                    <span class="badge badge-primary badge-pill ml-1" style="font-size: 9.5px; padding: 3px 8px;">Branch</span>
                </div>
            @endif

            <!-- Period Selector -->
            <div class="dropdown">
                <button class="pw-action-pill dropdown-toggle" type="button" data-toggle="dropdown" data-bs-toggle="dropdown">
                    <i class="far fa-calendar-alt text-primary"></i>
                    <span>{{ $periodLabel }}</span>
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow border-0" style="font-size: 12.5px; border-radius: 16px; padding: 8px;">
                    <a class="dropdown-item py-2 {{ $filterPeriod === 'all' ? 'fw-bold text-primary' : '' }}" href="{{ route('home', array_merge(request()->query(), ['period' => 'all'])) }}"><i class="fas fa-infinity me-1 text-primary"></i> All Time</a>
                    <a class="dropdown-item py-2 {{ $filterPeriod === 'today' ? 'fw-bold text-primary' : '' }}" href="{{ route('home', array_merge(request()->query(), ['period' => 'today'])) }}">Today</a>
                    <a class="dropdown-item py-2 {{ $filterPeriod === 'yesterday' ? 'fw-bold text-primary' : '' }}" href="{{ route('home', array_merge(request()->query(), ['period' => 'yesterday'])) }}">Yesterday</a>
                    <a class="dropdown-item py-2 {{ $filterPeriod === 'this_week' ? 'fw-bold text-primary' : '' }}" href="{{ route('home', array_merge(request()->query(), ['period' => 'this_week'])) }}">This Week</a>
                    <a class="dropdown-item py-2 {{ $filterPeriod === 'this_month' ? 'fw-bold text-primary' : '' }}" href="{{ route('home', array_merge(request()->query(), ['period' => 'this_month'])) }}">This Month</a>
                    <a class="dropdown-item py-2 {{ $filterPeriod === 'last_month' ? 'fw-bold text-primary' : '' }}" href="{{ route('home', array_merge(request()->query(), ['period' => 'last_month'])) }}">Last Month</a>
                    <a class="dropdown-item py-2 {{ $filterPeriod === 'this_year' ? 'fw-bold text-primary' : '' }}" href="{{ route('home', array_merge(request()->query(), ['period' => 'this_year'])) }}">This Year</a>
                </div>
            </div>

            <button type="button" class="pw-refresh-pill" onclick="refreshDashboardData()">
                <i class="fas fa-rotate" id="btnRefreshIcon"></i>
                <span>Refresh</span>
            </button>
        </div>
    </div>

    <!-- ─── TIER 1: TOP 4 3D GLOSSY PILL / TABLET CARDS ─────────────────────── -->
    <div class="pw-kpi-4row">

        <!-- Card 1: Total Sales (Blue Glass) -->
        <a href="{{ route('sale.index') }}" class="pw-pill-card pw-pill-blue">
            <div class="d-flex align-items-center">
                <div class="pw-3d-box pw-3d-blue">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="pw-pill-content">
                    <div class="pw-pill-title">Total Sales</div>
                    <div class="pw-pill-amount">
                        Rs. {{ number_format($salesPeriod, 0) }}
                    </div>
                    <div class="pw-pill-trend {{ ($growth['sales']['direction'] ?? 'up') === 'down' ? 'pw-trend-red' : 'pw-trend-green' }}">
                        <i class="fas fa-caret-{{ $growth['sales']['direction'] ?? 'up' }}"></i> {{ $growth['sales']['text'] ?? '0%' }} <span class="text-muted fw-normal">vs prev period</span>
                    </div>
                </div>
            </div>
            <!-- Sparkline 5 Bars -->
            <div class="pw-sparkline-bars">
                <div class="pw-spark-bar pw-spark-blue" style="height: 35%;"></div>
                <div class="pw-spark-bar pw-spark-blue" style="height: 55%;"></div>
                <div class="pw-spark-bar pw-spark-blue" style="height: 45%;"></div>
                <div class="pw-spark-bar pw-spark-blue" style="height: 80%;"></div>
                <div class="pw-spark-bar pw-spark-blue" style="height: 100%;"></div>
            </div>
        </a>

        <!-- Card 2: Total Purchases (Green Glass) -->
        <a href="{{ route('purchase.quick_create') }}" class="pw-pill-card pw-pill-green">
            <div class="d-flex align-items-center">
                <div class="pw-3d-box pw-3d-green">
                    <i class="fas fa-bag-shopping"></i>
                </div>
                <div class="pw-pill-content">
                    <div class="pw-pill-title">Total Purchases</div>
                    <div class="pw-pill-amount">
                        Rs. {{ number_format($purchasesPeriod, 0) }}
                    </div>
                    <div class="pw-pill-trend {{ ($growth['purchases']['direction'] ?? 'up') === 'down' ? 'pw-trend-red' : 'pw-trend-green' }}">
                        <i class="fas fa-caret-{{ $growth['purchases']['direction'] ?? 'up' }}"></i> {{ $growth['purchases']['text'] ?? '0%' }} <span class="text-muted fw-normal">vs prev period</span>
                    </div>
                    @if($filterPeriod !== 'all' && ($purchasesLifetime ?? 0) > 0)
                        <div style="font-size: 10.5px; color: #475569; font-weight: 600; margin-top: 3px;">
                            <i class="fas fa-database me-1 text-muted"></i>All-Time: Rs. {{ number_format($purchasesLifetime, 0) }}
                        </div>
                    @endif
                </div>
            </div>
            <!-- Sparkline 5 Bars -->
            <div class="pw-sparkline-bars">
                <div class="pw-spark-bar pw-spark-green" style="height: 30%;"></div>
                <div class="pw-spark-bar pw-spark-green" style="height: 50%;"></div>
                <div class="pw-spark-bar pw-spark-green" style="height: 70%;"></div>
                <div class="pw-spark-bar pw-spark-green" style="height: 60%;"></div>
                <div class="pw-spark-bar pw-spark-green" style="height: 95%;"></div>
            </div>
        </a>

        <!-- Card 3: Sales Returns (Orange Glass) -->
        <a href="{{ route('sale.return.index') }}" class="pw-pill-card pw-pill-orange">
            <div class="d-flex align-items-center">
                <div class="pw-3d-box pw-3d-orange">
                    <i class="fas fa-arrows-rotate"></i>
                </div>
                <div class="pw-pill-content">
                    <div class="pw-pill-title">Sales Returns</div>
                    <div class="pw-pill-amount">
                        Rs. {{ number_format($salesReturnsPeriod, 0) }}
                    </div>
                    <div class="pw-pill-trend {{ ($growth['sales_returns']['direction'] ?? 'up') === 'down' ? 'pw-trend-red' : 'pw-trend-green' }}">
                        <i class="fas fa-caret-{{ $growth['sales_returns']['direction'] ?? 'up' }}"></i> {{ $growth['sales_returns']['text'] ?? '0%' }} <span class="text-muted fw-normal">vs prev period</span>
                    </div>
                </div>
            </div>
            <!-- Sparkline 5 Bars -->
            <div class="pw-sparkline-bars">
                <div class="pw-spark-bar pw-spark-orange" style="height: 40%;"></div>
                <div class="pw-spark-bar pw-spark-orange" style="height: 30%;"></div>
                <div class="pw-spark-bar pw-spark-orange" style="height: 55%;"></div>
                <div class="pw-spark-bar pw-spark-orange" style="height: 75%;"></div>
                <div class="pw-spark-bar pw-spark-orange" style="height: 90%;"></div>
            </div>
        </a>

        <!-- Card 4: Net Profit (Purple Glass) -->
        <a href="{{ route('report.profit_loss') }}" class="pw-pill-card pw-pill-purple">
            <div class="d-flex align-items-center">
                <div class="pw-3d-box pw-3d-purple">
                    <i class="fas fa-coins text-warning"></i>
                </div>
                <div class="pw-pill-content">
                    <div class="pw-pill-title">Net Profit</div>
                    <div class="pw-pill-amount">
                        Rs. {{ number_format($netProfitPeriod ?? 0, 0) }}
                    </div>
                    <div class="pw-pill-trend {{ ($growth['net_profit']['direction'] ?? 'up') === 'down' ? 'pw-trend-red' : 'pw-trend-green' }}">
                        <i class="fas fa-caret-{{ $growth['net_profit']['direction'] ?? 'up' }}"></i> {{ $growth['net_profit']['text'] ?? '0%' }} <span class="text-muted fw-normal">vs prev period</span>
                    </div>
                </div>
            </div>
            <!-- Sparkline 5 Bars -->
            <div class="pw-sparkline-bars">
                <div class="pw-spark-bar pw-spark-purple" style="height: 25%;"></div>
                <div class="pw-spark-bar pw-spark-purple" style="height: 45%;"></div>
                <div class="pw-spark-bar pw-spark-purple" style="height: 60%;"></div>
                <div class="pw-spark-bar pw-spark-purple" style="height: 80%;"></div>
                <div class="pw-spark-bar pw-spark-purple" style="height: 100%;"></div>
            </div>
        </a>

    </div>

    <!-- ─── TIER 1.5: EXECUTIVE SECONDARY 3D METRICS BAR ──────────────────────── -->
    <div class="pw-exec-strip">
        <a href="{{ route('report.customer.ledger') }}" class="pw-exec-item">
            <div class="pw-exec-icon pw-exec-icon-blue"><i class="fas fa-hand-holding-dollar"></i></div>
            <div>
                <div class="pw-exec-lbl">Receivables</div>
                <div class="pw-exec-val">Rs. {{ number_format($totalReceivables ?? 0, 0) }}</div>
            </div>
        </a>
        <a href="{{ route('report.vendor.ledger') }}" class="pw-exec-item">
            <div class="pw-exec-icon pw-exec-icon-purple"><i class="fas fa-file-invoice-dollar"></i></div>
            <div>
                <div class="pw-exec-lbl">Payables</div>
                <div class="pw-exec-val">Rs. {{ number_format($totalPayables ?? 0, 0) }}</div>
            </div>
        </a>
        <a href="{{ route('view_all') }}" class="pw-exec-item">
            <div class="pw-exec-icon pw-exec-icon-emerald"><i class="fas fa-wallet"></i></div>
            <div>
                <div class="pw-exec-lbl">Cash in Hand</div>
                <div class="pw-exec-val">Rs. {{ number_format($totalCashBalance ?? 0, 0) }}</div>
            </div>
        </a>
        <a href="{{ route('view_all') }}" class="pw-exec-item">
            <div class="pw-exec-icon pw-exec-icon-cyan"><i class="fas fa-building-columns"></i></div>
            <div>
                <div class="pw-exec-lbl">Bank Balance</div>
                <div class="pw-exec-val">Rs. {{ number_format($totalBankBalance ?? 0, 0) }}</div>
            </div>
        </a>
        <a href="{{ route('all_expense_vochers') }}" class="pw-exec-item">
            <div class="pw-exec-icon pw-exec-icon-amber"><i class="fas fa-money-bill-transfer"></i></div>
            <div>
                <div class="pw-exec-lbl">Expenses</div>
                <div class="pw-exec-val">Rs. {{ number_format($expensesPeriod ?? 0, 0) }}</div>
            </div>
        </a>
        <a href="{{ route('purchase.return.index') }}" class="pw-exec-item">
            <div class="pw-exec-icon pw-exec-icon-rose"><i class="fas fa-rotate-left"></i></div>
            <div>
                <div class="pw-exec-lbl">Purch. Returns</div>
                <div class="pw-exec-val">Rs. {{ number_format($purchasesReturnsPeriod ?? 0, 0) }}</div>
            </div>
        </a>
    </div>

    <!-- ─── TIER 2: 3 WHITE GLASS CONTAINER CARDS ───────────────────────────── -->
    <div class="pw-grid-tier2">

        <!-- 1. Sales & Purchases Overview (Dual Line Area Chart) -->
        <div class="pw-white-card">
            <div class="pw-card-header">
                <div class="pw-card-title-group">
                    <i class="fas fa-chart-line pw-card-title-icon text-primary"></i>
                    <h5 class="pw-card-title">Sales &amp; Purchases Overview</h5>
                </div>
                <span class="pw-pill-select">{{ $periodLabel }}</span>
            </div>

            <!-- Chart Legend -->
            <div class="pw-chart-legend">
                <span><span class="pw-dot pw-dot-blue"></span> Sales</span>
                <span><span class="pw-dot pw-dot-purple"></span> Purchases</span>
            </div>

            <!-- Floating Tooltip Pill (matching screenshot) -->
            <div class="pw-floating-pill">
                Rs. {{ number_format($salesPeriod, 0) }} Sales
            </div>

            <div style="height: 220px; position: relative;">
                <canvas id="chartOverview"></canvas>
            </div>
        </div>

        <!-- 2. Business Analytics (Donut + Revenue/Expense Pills) -->
        <div class="pw-white-card">
            <div class="pw-card-header">
                <div class="pw-card-title-group">
                    <i class="fas fa-chart-pie pw-card-title-icon" style="color: #8b5cf6;"></i>
                    <h5 class="pw-card-title">Business Analytics</h5>
                </div>
                <span class="pw-pill-select">{{ $periodLabel }}</span>
            </div>

            <!-- Donut + Legend Row -->
            <div class="d-flex align-items-center justify-content-between px-2">
                <div class="pw-donut-wrap">
                    <canvas id="chartBusinessDonut"></canvas>
                    <div class="pw-donut-center">
                        <div class="pw-donut-val">{{ $businessAnalytics['sales_ratio'] }}%</div>
                        <div class="pw-donut-lbl">Sales Growth</div>
                    </div>
                </div>

                <div class="d-flex flex-column gap-2" style="font-size: 12.5px; font-weight: 700;">
                    <div class="d-flex align-items-center justify-content-between" style="min-width: 130px;">
                        <span><span class="pw-dot pw-dot-blue"></span> Sales</span>
                        <span class="text-dark">{{ $businessAnalytics['sales_ratio'] }}%</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between" style="min-width: 130px;">
                        <span><span class="pw-dot pw-dot-purple"></span> Purchases</span>
                        <span class="text-dark">{{ $businessAnalytics['purchases_ratio'] }}%</span>
                    </div>
                </div>
            </div>

            <!-- Bottom 2 Pills: Total Revenue & Total Expenses -->
            <div class="pw-analytics-pills">
                <div class="pw-sub-pill pw-sub-pill-green">
                    <div class="pw-sub-pill-icon text-success">
                        <i class="fas fa-arrow-trend-up"></i>
                    </div>
                    <div>
                        <div class="pw-sub-pill-lbl">Total Revenue</div>
                        <div class="pw-sub-pill-val">{{ $businessAnalytics['total_revenue_formatted'] ?? 'Rs. ' . number_format($businessAnalytics['total_revenue'], 0) }}</div>
                    </div>
                </div>

                <div class="pw-sub-pill pw-sub-pill-purple">
                    <div class="pw-sub-pill-icon" style="color: #8b5cf6;">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <div class="pw-sub-pill-lbl">Total Expenses</div>
                        <div class="pw-sub-pill-val">{{ $businessAnalytics['total_expenses_formatted'] ?? 'Rs. ' . number_format($businessAnalytics['total_expenses'], 0) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Recent Activities (Live Vertical Timeline) -->
        <div class="pw-white-card">
            <div class="pw-card-header">
                <div class="pw-card-title-group">
                    <i class="fas fa-bolt pw-card-title-icon text-warning"></i>
                    <h5 class="pw-card-title">Recent Activities</h5>
                </div>
                <a href="{{ route('sale.index') }}" class="pw-link-all">View All <i class="fas fa-arrow-right" style="font-size: 10px;"></i></a>
            </div>

            <div class="pw-timeline-list" id="activityFeedTier2">
                @forelse($liveActivities->take(4) as $act)
                @php
                    $nodeColor = match($act->module) {
                        'sales' => 'pw-node-green',
                        'purchases' => 'pw-node-blue',
                        'finance' => 'pw-node-amber',
                        default => 'pw-node-purple',
                    };
                    $tagColor = match($act->module) {
                        'sales' => 'pw-tag-green',
                        'purchases' => 'pw-tag-blue',
                        'finance' => 'pw-tag-amber',
                        default => 'pw-tag-purple',
                    };
                    $tagLabel = ucfirst($act->module == 'finance' ? 'Payment' : ($act->module == 'stock' ? 'Inventory' : $act->module));
                @endphp
                <div class="pw-timeline-item">
                    <div class="pw-timeline-left">
                        <div class="pw-timeline-node {{ $nodeColor }}">
                            <i class="fas {{ $act->icon ?: 'fa-bell' }}"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="pw-timeline-title">{{ $act->title }}</div>
                            <div class="pw-timeline-sub">by {{ $act->user_name ?: 'System' }} &bull; {{ $act->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    <span class="pw-tag-pill {{ $tagColor }}">{{ $tagLabel }}</span>
                </div>
                @empty
                <!-- Fallback Mock Activities matching screenshot -->
                <div class="pw-timeline-item">
                    <div class="pw-timeline-left">
                        <div class="pw-timeline-node pw-node-green"><i class="fas fa-shopping-cart"></i></div>
                        <div>
                            <div class="pw-timeline-title">New Sale #INV-2026-118 created</div>
                            <div class="pw-timeline-sub">by Ahmed Ali &bull; 2 min ago</div>
                        </div>
                    </div>
                    <span class="pw-tag-pill pw-tag-green">Sale</span>
                </div>

                <div class="pw-timeline-item">
                    <div class="pw-timeline-left">
                        <div class="pw-timeline-node pw-node-blue"><i class="fas fa-bag-shopping"></i></div>
                        <div>
                            <div class="pw-timeline-title">Purchase #PUR-2026-067 added</div>
                            <div class="pw-timeline-sub">by Sara Khan &bull; 5 min ago</div>
                        </div>
                    </div>
                    <span class="pw-tag-pill pw-tag-blue">Purchase</span>
                </div>

                <div class="pw-timeline-item">
                    <div class="pw-timeline-left">
                        <div class="pw-timeline-node pw-node-amber"><i class="fas fa-coins"></i></div>
                        <div>
                            <div class="pw-timeline-title">Payment received: Rs. 250,000</div>
                            <div class="pw-timeline-sub">from ABC Traders &bull; 8 min ago</div>
                        </div>
                    </div>
                    <span class="pw-tag-pill pw-tag-amber">Payment</span>
                </div>

                <div class="pw-timeline-item">
                    <div class="pw-timeline-left">
                        <div class="pw-timeline-node pw-node-purple"><i class="fas fa-cubes"></i></div>
                        <div>
                            <div class="pw-timeline-title">Stock updated: 500 units</div>
                            <div class="pw-timeline-sub">of Rice 5kg &bull; 12 min ago</div>
                        </div>
                    </div>
                    <span class="pw-tag-pill pw-tag-purple">Inventory</span>
                </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- ─── TIER 3: 4 WHITE GLASS CONTAINER CARDS ───────────────────────────── -->
    <div class="pw-grid-tier3">

        <!-- 1. Top Selling Products -->
        <div class="pw-white-card">
            <div class="pw-card-header">
                <div class="pw-card-title-group">
                    <i class="fas fa-trophy pw-card-title-icon text-warning"></i>
                    <h5 class="pw-card-title">Top Selling Products</h5>
                </div>
                <span class="pw-pill-select">This Month <i class="fas fa-chevron-down" style="font-size: 9px;"></i></span>
            </div>

            <div class="d-flex flex-column justify-content-center flex-grow-1">
                @foreach($displayTopProducts->take(3) as $prod)
                <div class="pw-prod-item">
                    <div class="pw-prod-thumb">
                        <i class="fas {{ $prod['icon'] ?? 'fa-box-open' }}" style="color: {{ $prod['color'] ?? '#2563eb' }};"></i>
                    </div>
                    <div class="pw-prod-info">
                        <div class="pw-prod-name">{{ $prod['name'] }}</div>
                        <div class="pw-prod-bar-bg">
                            <div class="pw-prod-bar-fill" style="width: {{ $prod['pct'] }}%; background: {{ $prod['color'] ?? '#2563eb' }};"></div>
                        </div>
                    </div>
                    <div class="pw-prod-pcs">{{ $prod['qty'] }}</div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- 2. Stock Status -->
        <div class="pw-white-card">
            <div class="pw-card-header">
                <div class="pw-card-title-group">
                    <i class="fas fa-cubes-stacked pw-card-title-icon text-primary"></i>
                    <h5 class="pw-card-title">Stock Status</h5>
                </div>
                <span class="pw-pill-select">All Warehouses <i class="fas fa-chevron-down" style="font-size: 9px;"></i></span>
            </div>

            <div class="d-flex align-items-center justify-content-between px-1 flex-grow-1">
                <div class="pw-donut-wrap">
                    <canvas id="chartStockStatus"></canvas>
                    <div class="pw-donut-center">
                        <div class="pw-donut-val">{{ $stockStatus['in_stock'] }}%</div>
                        <div class="pw-donut-lbl">In Stock</div>
                    </div>
                </div>

                <div class="d-flex flex-column gap-2" style="font-size: 12px; font-weight: 700;">
                    <div class="d-flex align-items-center justify-content-between" style="min-width: 105px;">
                        <span><span class="pw-dot pw-dot-green"></span> In Stock</span>
                        <span class="text-dark">{{ $stockStatus['in_stock'] }}%</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between" style="min-width: 105px;">
                        <span><span class="pw-dot pw-dot-orange"></span> Low Stock</span>
                        <span class="text-dark">{{ $stockStatus['low_stock'] }}%</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between" style="min-width: 105px;">
                        <span><span class="pw-dot pw-dot-red"></span> Out of Stock</span>
                        <span class="text-dark">{{ $stockStatus['out_of_stock'] }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Financial Overview -->
        <div class="pw-white-card">
            <div class="pw-card-header">
                <div class="pw-card-title-group">
                    <i class="fas fa-calculator pw-card-title-icon text-primary"></i>
                    <h5 class="pw-card-title">Financial Overview</h5>
                </div>
                <span class="pw-pill-select">{{ $periodLabel }}</span>
            </div>

            <!-- Curved Blue Wave Line Area Chart -->
            <div style="height: 105px; position: relative;">
                <canvas id="chartFinancialWave"></canvas>
            </div>

            <!-- Bottom 3 Pills (Income, Expenses, Profit) -->
            <div class="pw-fin-pills">
                <div class="pw-fin-pill pw-fin-green">
                    <div class="pw-fin-val">Rs. {{ number_format($financialOverview['income'] ?? 0, 0) }}</div>
                    <div class="pw-fin-lbl">Income</div>
                </div>
                <div class="pw-fin-pill pw-fin-red">
                    <div class="pw-fin-val">Rs. {{ number_format($financialOverview['expenses'] ?? 0, 0) }}</div>
                    <div class="pw-fin-lbl">Expenses</div>
                </div>
                <div class="pw-fin-pill pw-fin-purple">
                    <div class="pw-fin-val">Rs. {{ number_format($financialOverview['profit'] ?? 0, 0) }}</div>
                    <div class="pw-fin-lbl">Profit</div>
                </div>
            </div>
        </div>

        <!-- 4. Upcoming Tasks -->
        <div class="pw-white-card">
            <div class="pw-card-header">
                <div class="pw-card-title-group">
                    <i class="fas fa-circle-check pw-card-title-icon text-primary"></i>
                    <h5 class="pw-card-title">Upcoming Tasks</h5>
                </div>
                <a href="javascript:void(0)" onclick="openAddTaskModal()" class="pw-link-all">View All <i class="fas fa-arrow-right" style="font-size: 10px;"></i></a>
            </div>

            <div class="pw-task-list flex-grow-1" id="taskChecklist">
                @forelse($tasks->take(3) as $t)
                @php
                    $dueText = $t->due_date ? ($t->due_date->isToday() ? 'Today' : ($t->due_date->isTomorrow() ? 'Tomorrow' : $t->due_date->format('M d'))) : 'Pending';
                    $tagClass = match($t->priority) {
                        'urgent', 'high' => 'pw-tag-urgent',
                        'medium' => 'pw-tag-medium',
                        default => 'pw-tag-low',
                    };
                @endphp
                <div class="pw-task-row" id="taskTier3_{{ $t->id }}">
                    <input type="checkbox" class="pw-task-checkbox" onchange="toggleTaskStatusTier3({{ $t->id }})" {{ $t->status === 'completed' ? 'checked' : '' }}>
                    <div class="min-w-0 flex-grow-1">
                        <div class="pw-task-text" style="{{ $t->status === 'completed' ? 'text-decoration: line-through; opacity: 0.6;' : '' }}">{{ $t->title }}</div>
                        <div class="pw-task-tag">{{ $dueText }} &bull; <span class="{{ $tagClass }}">{{ ucfirst($t->priority) }}</span></div>
                    </div>
                </div>
                @empty
                <!-- Fallback FMCG Operations Tasks -->
                <div class="pw-task-row">
                    <input type="checkbox" class="pw-task-checkbox">
                    <div>
                        <div class="pw-task-text">Inspect Near-Expiry Batches in Main Warehouse</div>
                        <div class="pw-task-tag">Today &bull; <span class="pw-tag-urgent">Urgent</span></div>
                    </div>
                </div>
                <div class="pw-task-row">
                    <input type="checkbox" class="pw-task-checkbox">
                    <div>
                        <div class="pw-task-text">Dispatch Branch Stock Transfer #TR-104 to Lahore Branch</div>
                        <div class="pw-task-tag">Today &bull; <span class="pw-tag-urgent">High</span></div>
                    </div>
                </div>
                <div class="pw-task-row">
                    <input type="checkbox" class="pw-task-checkbox">
                    <div>
                        <div class="pw-task-text">Follow up Pending Recovery from City Distributors</div>
                        <div class="pw-task-tag">Tomorrow &bull; <span class="pw-tag-medium">High</span></div>
                    </div>
                </div>
                <div class="pw-task-row">
                    <input type="checkbox" class="pw-task-checkbox">
                    <div>
                        <div class="pw-task-text">Reorder Fast-Moving Stock (Sunridge &amp; Nestle)</div>
                        <div class="pw-task-tag">In 2 Days &bull; <span class="pw-tag-low">Medium</span></div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- ─── TIER 4: MULTI-BRANCH ENTERPRISE MATRIX & FMCG EXPIRY WATCHDOG ───── -->
    <div class="pw-grid-tier4">

        <!-- 1. FMCG Batch Expiry & Shelf-Life Watchdog (Left) -->
        <div class="pw-white-card">
            <div class="pw-card-header">
                <div class="pw-card-title-group">
                    <i class="fas fa-clock-rotate-left pw-card-title-icon text-danger"></i>
                    <div>
                        <h5 class="pw-card-title">FMCG Batch Expiry &amp; Shelf-Life Watchdog</h5>
                        <p class="text-muted mb-0" style="font-size: 11.5px; font-weight: 500;">Critical expiry tracking across active warehouse batches</p>
                    </div>
                </div>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1" style="font-size: 11px; font-weight: 800;">
                    <i class="fas fa-shield-virus me-1"></i> Quality Control
                </span>
            </div>

            <!-- Top 3 Expiry Status Mini-Cards -->
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <div class="p-2 rounded-3 border" style="background: #fef2f2; border-color: rgba(239, 68, 68, 0.25) !important;">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="text-danger fw-bold" style="font-size: 11px;"><i class="fas fa-ban me-1"></i> Expired Stock</span>
                            <span class="badge bg-danger text-white rounded-pill" style="font-size: 9.5px;">{{ $expiryStats['expired_count'] }} Batches</span>
                        </div>
                        <div class="fw-extrabold text-dark" style="font-size: 1.15rem; font-family: var(--f-display);">{{ $expiryStats['expired_val_formatted'] }}</div>
                        <div class="text-muted" style="font-size: 10.5px; font-weight: 600;">{{ number_format($expiryStats['expired_qty']) }} Pcs for disposal/return</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-2 rounded-3 border" style="background: #fffbeb; border-color: rgba(245, 158, 11, 0.25) !important;">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="text-warning-emphasis fw-bold" style="font-size: 11px;"><i class="fas fa-hourglass-half me-1"></i> Near Expiry (&lt;90d)</span>
                            <span class="badge bg-warning text-dark rounded-pill" style="font-size: 9.5px;">{{ $expiryStats['near_count'] }} Batches</span>
                        </div>
                        <div class="fw-extrabold text-dark" style="font-size: 1.15rem; font-family: var(--f-display);">{{ $expiryStats['near_val_formatted'] }}</div>
                        <div class="text-muted" style="font-size: 10.5px; font-weight: 600;">{{ number_format($expiryStats['near_qty']) }} Pcs prioritize sales</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-2 rounded-3 border" style="background: #ecfdf5; border-color: rgba(16, 185, 129, 0.25) !important;">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="text-success fw-bold" style="font-size: 11px;"><i class="fas fa-circle-check me-1"></i> Safe Shelf-Life</span>
                            <span class="badge bg-success text-white rounded-pill" style="font-size: 9.5px;">Healthy</span>
                        </div>
                        <div class="fw-extrabold text-dark" style="font-size: 1.15rem; font-family: var(--f-display);">{{ number_format($expiryStats['safe_qty']) }} Pcs</div>
                        <div class="text-muted" style="font-size: 10.5px; font-weight: 600;">&gt; 90 days validity period</div>
                    </div>
                </div>
            </div>

            <!-- Urgent Expiring Batches Table -->
            <div class="table-responsive flex-grow-1">
                <table class="table table-sm table-hover align-middle mb-0" style="font-size: 12px;">
                    <thead class="table-light text-muted" style="font-size: 10.5px; font-weight: 800; text-transform: uppercase;">
                        <tr>
                            <th class="ps-2">Product &amp; Batch</th>
                            <th>Warehouse</th>
                            <th class="text-end">Qty</th>
                            <th class="text-center">Expiry Date</th>
                            <th class="text-end pe-2">Shelf Life Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($urgentExpiringList as $ub)
                        @php
                            $badgeClass = match($ub['status']) {
                                'expired' => 'pw-exp-expired',
                                'critical' => 'pw-exp-critical',
                                'warning' => 'pw-exp-warning',
                                default => 'pw-exp-safe',
                            };
                            $statusText = $ub['days_left'] < 0 ? 'EXPIRED' : ($ub['days_left'] <= 30 ? $ub['days_left'] . 'd Critical' : $ub['days_left'] . 'd Remaining');
                        @endphp
                        <tr>
                            <td class="ps-2 py-2">
                                <div class="fw-bold text-dark">{{ $ub['product'] }}</div>
                                <span class="badge bg-light text-secondary border font-monospace" style="font-size: 9.5px;">{{ $ub['batch_no'] }}</span>
                            </td>
                            <td class="text-muted">{{ $ub['warehouse'] }}</td>
                            <td class="text-end fw-bold">{{ number_format($ub['qty']) }}</td>
                            <td class="text-center text-secondary">{{ $ub['expiry_date'] }}</td>
                            <td class="text-end pe-2">
                                <span class="pw-expiry-badge {{ $badgeClass }}">
                                    @if($ub['status'] === 'critical' || $ub['status'] === 'expired')
                                        <i class="fas fa-circle-exclamation"></i>
                                    @endif
                                    {{ $statusText }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">
                                <i class="fas fa-check-double text-success me-1"></i> No critical batch expiries detected! All products are within safe shelf life.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. Multi-Branch Operations & Live Sync Matrix (Right) -->
        <div class="pw-white-card">
            <div class="pw-card-header">
                <div class="pw-card-title-group">
                    <i class="fas fa-network-wired pw-card-title-icon text-primary"></i>
                    <div>
                        <h5 class="pw-card-title">Multi-Branch Operations</h5>
                        <p class="text-muted mb-0" style="font-size: 11.5px; font-weight: 500;">Live sales &amp; procurement sync across distribution nodes</p>
                    </div>
                </div>
                <span class="pw-live-indicator"><span class="pw-pulse-dot"></span> {{ $allBranches->count() }} ACTIVE NODES</span>
            </div>

            <!-- Branch List Cards -->
            <div class="d-flex flex-column gap-2 flex-grow-1 justify-content-center">
                @foreach($multiBranchesSummary as $mb)
                <div class="pw-branch-card {{ $mb['is_selected'] ? 'active' : '' }}">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="pw-branch-badge-icon">
                                <i class="fas fa-store"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 13px;">{{ $mb['name'] }}</div>
                                <div class="text-muted" style="font-size: 10.5px;">{{ $mb['address'] }}</div>
                            </div>
                        </div>

                        @if($isSuperAdmin)
                            @if($mb['is_selected'])
                                <span class="badge bg-primary rounded-pill px-2 py-1" style="font-size: 10px;">
                                    <i class="fas fa-eye me-1"></i> Current Scope
                                </span>
                            @else
                                <a href="{{ route('home', array_merge(request()->query(), ['branch_id' => $mb['id']])) }}" 
                                   class="btn btn-outline-primary btn-sm rounded-pill py-0 px-2 fw-bold" style="font-size: 11px;">
                                    Filter Scope <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            @endif
                        @else
                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1" style="font-size: 10px;">
                                <i class="fas fa-check-circle me-1"></i> Connected
                            </span>
                        @endif
                    </div>

                    <!-- Metrics row -->
                    <div class="row g-2 text-center pt-1 border-top" style="font-size: 11px;">
                        <div class="col-4 border-end">
                            <span class="text-muted d-block" style="font-size: 9.5px; font-weight: 700; text-transform: uppercase;">Sales</span>
                            <span class="fw-bold text-primary">{{ $mb['sales_formatted'] }}</span>
                        </div>
                        <div class="col-4 border-end">
                            <span class="text-muted d-block" style="font-size: 9.5px; font-weight: 700; text-transform: uppercase;">Procurement</span>
                            <span class="fw-bold text-purple" style="color: #8b5cf6;">{{ $mb['purchases_formatted'] }}</span>
                        </div>
                        <div class="col-4">
                            <span class="text-muted d-block" style="font-size: 9.5px; font-weight: 700; text-transform: uppercase;">Warehouses</span>
                            <span class="fw-bold text-dark">{{ $mb['warehouses_count'] }} Units</span>
                        </div>
                    </div>

                    <!-- Market Share Progress Bar -->
                    <div class="mt-2">
                        <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 10px; font-weight: 700; color: #64748b;">
                            <span>Branch Share</span>
                            <span>{{ $mb['sales_share'] }}%</span>
                        </div>
                        <div class="progress" style="height: 4px; background: #e2e8f0; border-radius: 4px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ max(8, $mb['sales_share']) }}%; border-radius: 4px;"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Footer Branding -->
    <div class="text-center pt-4 text-muted" style="font-size: 12px; font-weight: 600;">
        <span class="text-dark fw-bold">ProWave Technologies</span> &bull; FMGC Enterprise Business System &bull; 
        <span>Smart Business. Smart Growth.</span>
    </div>

</div>

<!-- Modal: Quick Add Task -->
<div class="modal fade" id="modalQuickTask" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-plus-circle text-primary me-2"></i>New Upcoming Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form onsubmit="event.preventDefault(); submitQuickTask();">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Task Description *</label>
                        <input type="text" id="quickTaskTitle" class="form-control form-control-sm" required placeholder="e.g. Call supplier for pending order">
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Priority</label>
                            <select id="quickTaskPriority" class="form-select form-select-sm">
                                <option value="high">High</option>
                                <option value="medium" selected>Medium</option>
                                <option value="low">Low</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Due Date</label>
                            <input type="date" id="quickTaskDueDate" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light btn-sm fw-bold px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold px-4">Add Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // ── CHART 1: SALES & PURCHASES OVERVIEW (100% DYNAMIC FROM CONTROLLER) ───
    function renderOverviewChart() {
        const ctx = document.getElementById('chartOverview');
        if (!ctx) return;

        const chartCtx = ctx.getContext('2d');

        // Blue Gradient for Sales
        const blueGrad = chartCtx.createLinearGradient(0, 0, 0, 220);
        blueGrad.addColorStop(0, 'rgba(37, 99, 235, 0.28)');
        blueGrad.addColorStop(1, 'rgba(37, 99, 235, 0.00)');

        // Purple Gradient for Purchases
        const purpleGrad = chartCtx.createLinearGradient(0, 0, 0, 220);
        purpleGrad.addColorStop(0, 'rgba(139, 92, 246, 0.25)');
        purpleGrad.addColorStop(1, 'rgba(139, 92, 246, 0.00)');

        const ovLabels = {!! json_encode($chartOverviewLabels ?? []) !!};
        const ovSales = {!! json_encode($chartOverviewSales ?? []) !!};
        const ovPurch = {!! json_encode($chartOverviewPurchases ?? []) !!};

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ovLabels,
                datasets: [
                    {
                        label: 'Sales',
                        data: ovSales,
                        borderColor: '#2563eb',
                        backgroundColor: blueGrad,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#2563eb',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    },
                    {
                        label: 'Purchases',
                        data: ovPurch,
                        borderColor: '#a855f7',
                        backgroundColor: purpleGrad,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#a855f7',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(c) {
                                const val = c.raw !== undefined ? c.raw : (c.yLabel !== undefined ? c.yLabel : 0);
                                return (c.dataset ? c.dataset.label : '') + ': Rs. ' + Number(val).toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    xAxes: [{
                        gridLines: { display: false },
                        ticks: { fontColor: '#94a3b8', fontSize: 10, fontStyle: 'bold' }
                    }],
                    yAxes: [{
                        gridLines: { color: 'rgba(226, 232, 240, 0.6)' },
                        ticks: {
                            beginAtZero: true,
                            fontColor: '#94a3b8',
                            fontSize: 10,
                            fontStyle: 'bold',
                            callback: function(v) { 
                                if (v >= 1000000) return (v / 1000000).toFixed(1) + 'M';
                                if (v >= 1000) return (v / 1000).toFixed(0) + 'K';
                                return v;
                            }
                        }
                    }],
                    x: {
                        grid: { display: false },
                        ticks: { font: { weight: '600', size: 10 }, color: '#94a3b8' }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(v) { 
                                if (v >= 1000000) return (v / 1000000).toFixed(1) + 'M';
                                if (v >= 1000) return (v / 1000).toFixed(0) + 'K';
                                return v;
                            },
                            font: { weight: '600', size: 10 },
                            color: '#94a3b8'
                        },
                        grid: { color: 'rgba(226, 232, 240, 0.6)' }
                    }
                }
            }
        });
    }

    // ── CHART 2: BUSINESS ANALYTICS DONUT (100% DYNAMIC) ──────────────────────
    function renderBusinessDonut() {
        const ctx = document.getElementById('chartBusinessDonut');
        if (!ctx) return;

        const salesPct = {{ $businessAnalytics['sales_ratio'] ?? 0 }};
        const purchPct = {{ $businessAnalytics['purchases_ratio'] ?? 0 }};
        const hasData = (salesPct + purchPct) > 0;

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: hasData ? ['Sales', 'Purchases'] : ['No Data'],
                datasets: [{
                    data: hasData ? [salesPct, purchPct] : [1],
                    backgroundColor: hasData ? ['#06b6d4', '#a855f7'] : ['#e2e8f0'],
                    borderWidth: 0,
                    hoverOffset: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: false },
                tooltips: { enabled: true },
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                },
                cutoutPercentage: 72,
                cutout: '72%'
            }
        });
    }

    // ── CHART 3: STOCK STATUS DONUT (100% DYNAMIC) ───────────────────────────
    function renderStockDonut() {
        const ctx = document.getElementById('chartStockStatus');
        if (!ctx) return;

        const inStock = {{ $stockStatus['in_stock'] ?? 0 }};
        const lowStock = {{ $stockStatus['low_stock'] ?? 0 }};
        const outStock = {{ $stockStatus['out_of_stock'] ?? 0 }};
        const hasStock = (inStock + lowStock + outStock) > 0;

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: hasStock ? ['In Stock', 'Low Stock', 'Out of Stock'] : ['No Stock Data'],
                datasets: [{
                    data: hasStock ? [inStock, lowStock, outStock] : [1],
                    backgroundColor: hasStock ? ['#10b981', '#f59e0b', '#ef4444'] : ['#e2e8f0'],
                    borderWidth: 0,
                    hoverOffset: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: false },
                tooltips: { enabled: true },
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                },
                cutoutPercentage: 72,
                cutout: '72%'
            }
        });
    }

    // ── CHART 4: FINANCIAL OVERVIEW CURVED BLUE WAVE (100% DYNAMIC) ───────────
    function renderFinancialWave() {
        const ctx = document.getElementById('chartFinancialWave');
        if (!ctx) return;

        const chartCtx = ctx.getContext('2d');
        const blueGrad = chartCtx.createLinearGradient(0, 0, 0, 100);
        blueGrad.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
        blueGrad.addColorStop(1, 'rgba(59, 130, 246, 0.00)');

        const waveData = {!! json_encode($chartFinancialWaveData ?? []) !!};
        const waveLabels = {!! json_encode($chartFinancialWaveLabels ?? []) !!};

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: waveLabels,
                datasets: [{
                    data: waveData,
                    borderColor: '#2563eb',
                    backgroundColor: blueGrad,
                    fill: true,
                    tension: 0.45,
                    borderWidth: 2.5,
                    pointRadius: 3,
                    pointBackgroundColor: '#2563eb',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: false },
                tooltips: { enabled: true },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(c) {
                                const val = c.raw !== undefined ? c.raw : (c.yLabel !== undefined ? c.yLabel : 0);
                                return 'Revenue: Rs. ' + Number(val).toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    xAxes: [{
                        display: false,
                        gridLines: { display: false, drawBorder: false }
                    }],
                    yAxes: [{
                        display: false,
                        gridLines: { display: false, drawBorder: false }
                    }],
                    x: {
                        display: false,
                        grid: { display: false, drawBorder: false }
                    },
                    y: {
                        display: false,
                        grid: { display: false, drawBorder: false }
                    }
                }
            }
        });
    }
                    }],
                    yAxes: [{
                        display: false,
                        gridLines: { display: false, drawBorder: false }
                    }],
                    x: {
                        display: false,
                        grid: { display: false, drawBorder: false }
                    },
                    y: {
                        display: false,
                        grid: { display: false, drawBorder: false }
                    }
                }
            }
        });
    }

    // ── INTERACTIVE TASK HANDLING ─────────────────────────────────────────────
    function openAddTaskModal() {
        const modal = new bootstrap.Modal(document.getElementById('modalQuickTask'));
        modal.show();
    }

    function submitQuickTask() {
        const title = document.getElementById('quickTaskTitle').value;
        const priority = document.getElementById('quickTaskPriority').value;
        const dueDate = document.getElementById('quickTaskDueDate').value;

        if (!title.trim()) return;

        fetch('{{ route("dashboard.tasks.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                branch_id: '{{ $currentBranchId ?? "" }}',
                title: title,
                priority: priority,
                due_date: dueDate
            })
        })
        .then(res => res.json())
        .then(res => {
            if (res.ok) {
                const modalEl = document.getElementById('modalQuickTask');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
                document.getElementById('quickTaskTitle').value = '';

                const tagClass = priority === 'high' ? 'pw-tag-urgent' : (priority === 'medium' ? 'pw-tag-medium' : 'pw-tag-low');
                const html = `
                    <div class="pw-task-row" id="taskTier3_${res.task.id}">
                        <input type="checkbox" class="pw-task-checkbox" onchange="toggleTaskStatusTier3(${res.task.id})">
                        <div class="min-w-0 flex-grow-1">
                            <div class="pw-task-text">${res.task.title}</div>
                            <div class="pw-task-tag">Today &bull; <span class="${tagClass}">${priority.toUpperCase()}</span></div>
                        </div>
                    </div>
                `;
                document.getElementById('taskChecklist').insertAdjacentHTML('afterbegin', html);
            }
        });
    }

    function toggleTaskStatusTier3(id) {
        fetch(`/dashboard/tasks/${id}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(res => {
            const el = document.getElementById(`taskTier3_${id}`);
            if (el) {
                const textEl = el.querySelector('.pw-task-text');
                if (res.status === 'completed') {
                    textEl.style.textDecoration = 'line-through';
                    textEl.style.opacity = '0.6';
                } else {
                    textEl.style.textDecoration = 'none';
                    textEl.style.opacity = '1';
                }
            }
        });
    }

    // Live Activity Polling (every 15s)
    let lastActivityId = {{ $liveActivities->first()?->id ?? 0 }};
    function pollLiveActivities() {
        const branchParam = '{{ $currentBranchId ?? "all" }}';
        fetch(`{{ route("dashboard.live_activities") }}?after_id=${lastActivityId}&branch_id=${branchParam}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.ok && data.count > 0 && data.activities.length > 0) {
                lastActivityId = data.latest_id;
                const container = document.getElementById('activityFeedTier2');
                data.activities.forEach(act => {
                    const nodeClass = act.module === 'sales' ? 'pw-node-green' : (act.module === 'purchases' ? 'pw-node-blue' : (act.module === 'finance' ? 'pw-node-amber' : 'pw-node-purple'));
                    const tagClass = act.module === 'sales' ? 'pw-tag-green' : (act.module === 'purchases' ? 'pw-tag-blue' : (act.module === 'finance' ? 'pw-tag-amber' : 'pw-tag-purple'));
                    const tagLabel = act.module === 'finance' ? 'Payment' : (act.module === 'stock' ? 'Inventory' : act.module.charAt(0).toUpperCase() + act.module.slice(1));
                    const itemHtml = `
                        <div class="pw-timeline-item" style="animation: fadeIn 0.4s ease-out;">
                            <div class="pw-timeline-left">
                                <div class="pw-timeline-node ${nodeClass}"><i class="fas ${act.icon}"></i></div>
                                <div class="min-w-0">
                                    <div class="pw-timeline-title">${act.title}</div>
                                    <div class="pw-timeline-sub">by ${act.user_name} &bull; ${act.time_ago}</div>
                                </div>
                            </div>
                            <span class="pw-tag-pill ${tagClass}">${tagLabel}</span>
                        </div>
                    `;
                    container.insertAdjacentHTML('afterbegin', itemHtml);
                    // keep top 4
                    while (container.children.length > 4) {
                        container.removeChild(container.lastElementChild);
                    }
                });
            }
        })
        .catch(() => {});
    }

    // Refresh Dashboard Action
    function refreshDashboardData() {
        const icon = document.getElementById('btnRefreshIcon');
        if (icon) icon.classList.add('fa-spin');
        setTimeout(() => {
            window.location.reload();
        }, 500);
    }

    // Initialize all Charts on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        renderOverviewChart();
        renderBusinessDonut();
        renderStockDonut();
        renderFinancialWave();

        // Start background polling for real-time live events
        setInterval(pollLiveActivities, 15000);
    });
</script>
@endsection
