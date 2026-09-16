<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Proposal {{ $itinerary->reference_number }}</title>
    <style>
        @page {
            margin: 0;
            size: A4;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #222222;
        }

        /* =====================================================
           COVER PAGE
           Layout: bg image (full page) + content (277mm) + footer (20mm)
        ===================================================== */

        /* Background image — inside .cover-content (position:relative).
           Dompdf positions absolute elements relative to their nearest
           positioned ancestor, so this fills exactly the 277mm content area. */
        .cover-bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0;
        }

        /* Content section — exactly 277mm */
        .cover-content {
            position: relative;
            z-index: 1;
            width: 100%;
            height: 275mm;
            overflow: hidden;
        }

        /* All visible content sits on top of the bg image */
        .cover-inner {
            position: relative;
            z-index: 1;
            width: 100%;
            padding-top: 50px;
            padding-bottom: 50px;
            background: linear-gradient(to bottom, #1e1e2d 100%, transparent 20%);
        }

        /* Footer section — exactly 20mm, white bg, no image */
        .cover-footer {
            position: relative;
            width: 100%;
            height: 20mm;
            background: #ffffff;
            border-top: 2px solid #f5a623;
            display: table;
            box-sizing: border-box;
            padding: 0 30px;
        }

        .cover-footer-row {
            display: table-row;
            width: 100%;
            height: 20mm;
        }

        /* ---- Header Bar (dark strip at top) ---- */
        .cover-header-table {
            width: 100%;
            padding: 20px 40px;
            border-collapse: collapse;

        }

        .cover-header-pill {
            padding: 8px 16px;
            background: #f5a623;
            color: #000000;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            width: 1%;
            border-top: 1px solid #f5a623;
            border-bottom: 1px solid #f5a623;
            white-space: nowrap;
        }

        .cover-header-ref {
            padding: 8px 14px;
            color: #ffffff;
            font-size: 11px;
            white-space: nowrap;
            border-top: 1px solid #ffffff;
            border-bottom: 1px solid #ffffff;
        }

        .cover-header-client {
            padding: 8px 16px;
            color: #ffffff;
            font-size: 11px;
            border-top: 1px solid #ffffff;
            border-bottom: 1px solid #ffffff;
            letter-spacing: 1px;
            font-weight: bold;
            text-align: right;
            width: 99%;
        }

        /* ---- Stats Row (bordered boxes, white bg) ---- */
        .cover-stats-table {
            width: 100%;
            padding: 0 50px;
            margin-top: 40px border-collapse: collapse;
            border-radius: 3px;
        }

        .cover-stats-cell {
            padding: 10px 20px;
            border: 1px solid #d0d0d0;
            width: 25%;
            vertical-align: top;
        }

        .cover-stats-label {
            font-size: 8px;
            text-transform: uppercase;
            color: #d0d0d0;
            letter-spacing: 0.8px;
            margin-bottom: 4px;
        }

        .cover-stats-value {
            font-size: 13px;
            font-weight: bold;
            color: #ffffff;
        }

        /* ---- Title (white background) ---- */
        .cover-title-section {
            padding: 20px 40px;
        }

        .cover-title {
            font-size: 32px;
            font-weight: bold;
            color: #ffffff;
            line-height: 1.15;
        }

        /* ---- Intro Letter Box (dark charcoal) ---- */
        .cover-intro-box {
            margin: 40px;
            width: 80%;
            background: #1e1e2d;
            color: #ffffff;
            padding: 30px;
            opacity: 0.5;
        }

        .cover-intro-greeting {
            font-size: 13px;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 6px;
        }

        .cover-intro-body {
            font-size: 15px;
            color: #ffffff;
            line-height: 1.6em;
            margin-bottom: 12px;
        }

        .cover-intro-body p {
            margin-bottom: 8px;
            font-size: 15px;
            color: #ffffff;
            line-height: 1.6em;
        }

        .cover-intro-body strong {
            font-weight: bold;
            color: #ffffff;
        }

        .cover-intro-body ul,
        .cover-intro-body ol {
            margin-left: 16px;
            margin-bottom: 8px;
        }

        .cover-intro-body li {
            margin-bottom: 4px;
            color: #ffffff;
        }

        .cover-intro-body a {
            color: #f5a623;
        }

        .cover-intro-regards {
            font-size: 15px;
            color: #cccccc;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        /* Agent info inside intro box */
        .cover-agent-table {
            width: 100%;
            border-collapse: collapse;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            padding-top: 12px;
            margin-top: 4px;
        }

        .cover-agent-photo {
            width: 46px;
            height: 46px;
            border-radius: 3px;
            object-fit: cover;
        }

        .cover-agent-name {
            font-size: 12px;
            font-weight: bold;
            color: #ffffff;
        }

        .cover-agent-company {
            font-size: 10px;
            color: #aaaaaa;
            margin-top: 2px;
        }

        .cover-agent-label {
            font-size: 9px;
            color: #888888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding-right: 8px;
            padding-bottom: 4px;
            white-space: nowrap;
        }

        .cover-agent-value {
            font-size: 11px;
            color: #ffffff;
            font-weight: bold;
            padding-bottom: 4px;
        }

        /* ---- Cover Footer cells ---- */
        .cover-footer-left {
            display: table-cell;
            vertical-align: middle;
            width: 60%;
        }

        .cover-footer-right {
            display: table-cell;
            vertical-align: middle;
            width: 40%;
            text-align: right;
        }

        .cover-footer-app-name {
            font-size: 15px;
            font-weight: bold;
            color: #111111;
            margin-bottom: 2px;
        }

        .cover-footer-website {
            font-size: 18px;
            color: #f5a623;
        }

        .cover-footer-logo {
            max-height: 80px;
            max-width: 150px;
            object-fit: contain;
        }

        /* =====================================================
           SUMMARY PAGE (Page 2)
        ===================================================== */

        /* Full A4 page container — position:relative lets us pin the footer via absolute */
        .summary-page {
            page-break-before: always;
            position: relative;
            width: 100%;
            height: 297mm;
            overflow: hidden;
            background: #ffffff;
        }

        /* Inner wrapper — margin-based (not padding) to avoid Dompdf overflow */
        .sum-body {
            margin: 30px 40px 0 40px;
            padding-bottom: 30mm;
            /* keep content above the 20mm footer + buffer */
        }

        /* ── Summary badge header ── */
        .sum-header {
            width: 100%;
            border-bottom: 1px solid #e8a020;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .sum-badge {
            display: inline-block;
            background: #f5a623;
            color: #000000;
            font-weight: bold;
            font-size: 11px;
            padding: 5px 14px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        /* ── Tour title row ── */
        .sum-title-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .sum-cover-img {
            width: 120px;
            height: 80px;
            object-fit: cover;
        }

        .sum-title-cell {
            vertical-align: middle;
            padding-left: 18px;
        }

        .sum-tour-title {
            font-size: 24px;
            font-weight: bold;
            color: #111111;
            line-height: 1.25;
        }

        /* ── Date strip ── */
        .sum-dates-table {
            width: 100%;
            border-collapse: collapse;
            border-top: 1px solid #e0e0e0;
            border-bottom: 1px solid #e0e0e0;
            margin-bottom: 24px;
        }

        .sum-dates-cell {
            padding: 10px 16px;
            vertical-align: middle;
        }

        .sum-date-icon {
            font-size: 18px;
            color: #555555;
            padding: 10px 10px 10px 0;
            vertical-align: middle;
            width: 28px;
        }

        .sum-date-label {
            font-size: 10px;
            font-weight: bold;
            color: #333333;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .sum-date-value {
            font-size: 11px;
            color: #555555;
        }

        /* ── Day by Day section ── */
        .sum-dbd-title {
            font-size: 20px;
            font-weight: bold;
            color: #111111;
            margin-bottom: 10px;
        }

        .sum-info-row {
            font-size: 11px;
            color: #333333;
            margin-bottom: 6px;
        }

        .sum-info-row strong {
            font-weight: bold;
        }


        .sum-dest-meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .sum-dest-meta-left {
            font-size: 11px;
            color: #333333;
            vertical-align: middle;
        }

        .sum-dest-meta-right {
            font-size: 11px;
            color: #333333;
            text-align: right;
            vertical-align: middle;
        }

        .sum-dest-meta-right strong {
            font-weight: bold;
        }

        /* ── Day-by-day table ── */
        .sum-dbd-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 11px;
        }

        .sum-dbd-thead th {
            background: #f9f9f9;
            border: 1px solid #e0e0e0;
            padding: 8px 12px;
            font-size: 10px;
            font-weight: bold;
            color: #333333;
            text-align: left;
        }

        .sum-dbd-row td {
            border: 1px solid #e8e8e8;
            padding: 9px 12px;
            vertical-align: top;
            color: #333333;
        }

        .sum-dbd-dot {
            width: 18px;
            vertical-align: middle;
            text-align: center;
        }

        .sum-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #f5a623;
            border-radius: 3px;
        }

        .sum-day-label {
            font-weight: bold;
            color: #111111;
        }

        .sum-accomm-name {
            color: #111111;
            font-weight: normal;
        }

        .sum-accomm-sub {
            font-size: 10px;
            color: #888888;
            margin-top: 2px;
        }

        /* ── Summary page footer — pinned to page bottom ── */
        .sum-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 20mm;
            background: #ffffff;
            border-top: 1px solid #e0e0e0;
            display: table;
            width: 100%;
        }

        .sum-footer-row {
            display: table-row;
            width: 100%;
            height: 20mm;
        }

        .sum-footer-left {
            display: table-cell;
            vertical-align: middle;
            width: 60%;
            font-size: 10px;
            color: #888888;
            padding: 10px 0 0 40px;
        }

        .sum-footer-right {
            display: table-cell;
            vertical-align: middle;
            width: 40%;
            text-align: right;
            font-size: 11px;
            font-weight: bold;
            color: #111111;
            padding: 10px 40px 0 0;
        }

        /* =====================================================
           DAY PAGES (Page 3+)
        ===================================================== */

        .day-page {
            page-break-before: always;
            position: relative;
            width: 100%;
            height: 297mm;
            overflow: hidden;
            background: #ffffff;
        }

        .day-body {
            margin: 30px 40px 0 40px;
            padding-bottom: 30mm;
        }

        /* ── Day header bar ── */
        .day-header-table {
            width: 100%;
            border-collapse: collapse;
            background: #f9f9f9;
            border-bottom: 1px solid #e8a020;
            margin-bottom: 14px;
        }

        .day-num-badge {
            background: #f5a623;
            color: #000000;
            font-weight: bold;
            font-size: 12px;
            padding: 8px 10px;
            white-space: nowrap;
            width: 60px;
            text-align: center;
        }

        .day-num-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .day-date-cell {
            padding: 8px 14px;
            font-size: 12px;
            color: #333333;
        }

        .day-date-bold {
            font-weight: bold;
        }

        .day-location-cell {
            padding: 8px 14px;
            font-size: 11px;
            color: #555555;
            text-align: right;
            white-space: nowrap;
            width: 30%;
        }

        /* ── Two-column body ── */
        .day-cols-table {
            width: 100%;
            border-collapse: collapse;
            vertical-align: top;
        }

        .day-col-left {
            width: 54%;
            vertical-align: top;
            padding-right: 16px;
        }

        .day-col-right {
            width: 43%;
            vertical-align: top;
            padding-left: 4px;
        }

        /* ── Left column ── */
        .day-dest-title {
            font-size: 26px;
            font-weight: bold;
            color: #111111;
            margin-bottom: 8px;
            line-height: 1.2;
        }

        .day-description {
            font-size: 11px;
            color: #444444;
            line-height: 1.6;
            margin-bottom: 12px;
        }

        .day-description p {
            margin-bottom: 6px;
            font-size: 11px;
            line-height: 1.6;
            color: #444444;
        }

        .day-description strong {
            font-weight: bold;
            color: #111111;
        }

        .day-description ul,
        .day-description ol {
            margin-left: 14px;
            margin-bottom: 6px;
        }

        .day-description li {
            margin-bottom: 3px;
        }

        /* ── Accommodation block ── */
        .day-accomm-divider {
            border: none;
            border-top: 1px solid #e0e0e0;
            margin: 12px 0 10px;
        }

        .day-accomm-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        .day-accomm-icon-cell {
            width: 32px;
            vertical-align: top;
            padding-top: 2px;
        }

        .day-accomm-icon {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 1px solid #cccccc;
            text-align: center;
            font-size: 10px;
            line-height: 20px;
            color: #888888;
        }

        .day-accomm-meta {
            font-size: 9px;
            color: #888888;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 2px;
        }

        .day-accomm-name {
            font-size: 13px;
            font-weight: bold;
            color: #111111;
            margin-bottom: 1px;
        }

        .day-accomm-type {
            font-size: 10px;
            color: #888888;
        }

        .day-accomm-desc {
            font-size: 10px;
            color: #555555;
            line-height: 1.5;
            margin-bottom: 8px;
        }

        /* ── Images ── */
        .day-img-wrap {
            margin-top: 8px;
            position: relative;
        }

        .day-img-caption {
            font-size: 9px;
            color: #888888;
            margin-top: 2px;
            margin-bottom: 8px;
        }

        .day-img-full {
            width: 100%;
            display: block;
            object-fit: cover;
        }

        /* ── Right column ── */
        .day-activity-box {
            border: 1px solid #e0c88a;
            background: #fffdf5;
            padding: 10px 12px;
            margin-bottom: 10px;
        }

        .day-activity-title {
            font-size: 12px;
            color: #111111;
            margin-bottom: 8px;
        }

        .day-activity-title strong {
            font-weight: bold;
        }

        .day-activity-time {
            font-size: 10px;
            color: #888888;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 4px;
        }

        .day-activity-item {
            font-size: 11px;
            color: #222222;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .day-meal-box {
            border: 1px solid #e0c88a;
            border-left: 3px solid #f5a623;
            background: #fffdf5;
            padding: 8px 12px;
            margin-bottom: 10px;
        }

        .day-meal-title {
            font-size: 11px;
            font-weight: bold;
            color: #111111;
            margin-bottom: 4px;
        }

        .day-meal-value {
            font-size: 11px;
            color: #444444;
        }

        /* ── Gallery fill images ── */
        .day-gallery-img {
            width: 100%;
            display: block;
            object-fit: cover;
            margin-bottom: 6px;
        }

        /* ── Day page footer ── */
        .day-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 20mm;
            background: #ffffff;
            border-top: 1px solid #e0e0e0;
            display: table;
            width: 100%;
        }

        .day-footer-row {
            display: table-row;
            width: 100%;
            height: 20mm;
        }

        .day-footer-left {
            display: table-cell;
            vertical-align: middle;
            width: 60%;
            font-size: 10px;
            color: #888888;
            padding: 10px 0 0 40px;
        }

        .day-footer-right {
            display: table-cell;
            vertical-align: middle;
            width: 40%;
            text-align: right;
            font-size: 11px;
            font-weight: bold;
            color: #111111;
            padding: 10px 40px 0 0;
        }

        /* =====================================================
           PRICING PAGE
        ===================================================== */
        .pricing-table-top {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #e0c88a;
        }

        .pricing-table-top td {
            border: 1px solid #e0c88a;
            padding: 10px 15px;
            vertical-align: top;
            width: 33.333%;
        }

        .pricing-meta-label {
            font-size: 10px;
            color: #888888;
            margin-bottom: 4px;
        }

        .pricing-meta-value {
            font-size: 13px;
            font-weight: bold;
            color: #111111;
        }

        .pricing-inc-exc-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            border: 1px solid #e0c88a;
        }

        .pricing-inc-exc-table td {
            width: 50%;
            border: 1px solid #e0c88a;
            padding: 15px;
            vertical-align: top;
        }

        .pricing-inc-exc-title {
            font-size: 13px;
            font-weight: bold;
            color: #111111;
            margin-bottom: 10px;
        }

        .pricing-inc-icon {
            color: #4CAF50;
            font-size: 14px;
            margin-right: 4px;
        }

        .pricing-exc-icon {
            color: #F44336;
            font-size: 14px;
            margin-right: 4px;
        }

        .pricing-list {
            font-size: 11px;
            color: #444444;
            line-height: 1.6;
        }

        .pricing-section-title {
            font-size: 20px;
            font-weight: bold;
            color: #111111;
            margin-bottom: 12px;
            margin-top: 20px;
            font-family: 'Playfair Display', Georgia, serif;
        }

        .pricing-breakdown-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e0e0e0;
            background: #fffdf5;
            margin-bottom: 15px;
        }

        .pricing-breakdown-table td {
            border: 1px solid #e0e0e0;
            padding: 12px 15px;
            font-size: 12px;
            color: #111111;
        }

        .pricing-breakdown-total td {
            font-weight: bold;
        }

        .pricing-btn-container {
            padding: 15px;
            background: #fffdf5;
            border: 1px solid #e0e0e0;
            border-top: none;
            margin-top: -15px;
            margin-bottom: 20px;
        }

        .btn-booking {
            display: inline-block;
            border: 1px solid #4CAF50;
            color: #4CAF50;
            padding: 8px 16px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            background: #ffffff;
        }

        .payment-box {
            border: 1px solid #e0e0e0;
            padding: 20px;
            margin-bottom: 30px;
            font-size: 11px;
            line-height: 1.6;
            color: #333333;
        }

        .payment-title {
            font-size: 13px;
            font-weight: bold;
            color: #111111;
            margin-bottom: 8px;
        }

        .payment-subtitle {
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
            color: #111111;
        }

        .payment-text {
            margin-bottom: 10px;
        }

        .payment-text strong {
            font-weight: bold;
        }

        .optional-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e0c88a;
        }

        .optional-table th {
            background: #fffdf5;
            text-align: left;
            padding: 10px 15px;
            font-size: 12px;
            font-weight: bold;
            border: 1px solid #e0c88a;
            color: #111111;
        }

        .optional-table td {
            padding: 10px 15px;
            font-size: 11px;
            border: 1px solid #e0c88a;
            background: #fffdf5;
            color: #333333;
        }

        /* =====================================================
           ABOUT US PAGE
        ===================================================== */
        .about-title {
            font-size: 24px;
            font-weight: bold;
            color: #111111;
            margin-bottom: 12px;
            font-family: 'Playfair Display', Georgia, serif;
        }

        .about-text {
            font-size: 11px;
            color: #444444;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .contact-box {
            border: 1px solid #f5a623;
            background: #fffdf5;
            padding: 20px;
            margin-top: 15px;
        }

        .contact-box-title {
            font-size: 16px;
            font-weight: bold;
            color: #111111;
            margin-bottom: 15px;
            font-family: 'Playfair Display', Georgia, serif;
        }

        .contact-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            color: #444444;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .contact-table td {
            vertical-align: top;
            padding-bottom: 6px;
        }

        .contact-table td:first-child {
            font-weight: bold;
            color: #111111;
            width: 70px;
        }

        .contact-extra {
            font-size: 11px;
            color: #444444;
            line-height: 1.6;
            margin-top: 15px;
        }

        .contact-extra strong {
            color: #111111;
        }

        .about-logo {
            max-width: 180px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

    {{-- ==================== COVER PAGE ==================== --}}


    {{-- ── SECTION 1: Content (277mm) ── --}}
    <div class="cover-content">

        {{-- Background image: inside .cover-content so it fills only the 277mm area --}}
        @if($settings->proposal_heading_bg_image)
            @php $coverBgPath = str_replace('\\', '/', storage_path('app/public/' . $settings->proposal_heading_bg_image)); @endphp
            @if(file_exists($coverBgPath))
                <img class="cover-bg-image" src="{{ $coverBgPath }}" alt="">
            @endif
        @endif

        {{-- All visible content on top of the image (z-index:1) --}}
        <div class="cover-inner">

            {{-- Header Bar --}}
            <table class="cover-header-table">
                <tr>
                    <td class="cover-header-pill">{{ __('Proposal') }}</td>
                    <td class="cover-header-ref">Ref. Number: #{{ $itinerary->reference_number }}</td>
                    <td class="cover-header-client">{{ $itinerary->client_name ?: 'Valued Client' }}</td>
                </tr>
            </table>

            {{-- Title --}}
            <div class="cover-title-section">
                <h1 class="cover-title">{{ $itinerary->title }}</h1>
            </div>

            {{-- Stats Row --}}
            <table class="cover-stats-table">
                <tr>
                    <td class="cover-stats-cell">
                        <div class="cover-stats-label">{{ __('Tour Length') }}</div>
                        <div class="cover-stats-value">{{ $itinerary->days->count() }} Days</div>
                    </td>
                    <td class="cover-stats-cell">
                        <div class="cover-stats-label">{{ __('Travelers') }}</div>
                        <div class="cover-stats-value">{{ $itinerary->adults + $itinerary->children }}x Guest</div>
                    </td>
                    <td class="cover-stats-cell">
                        <div class="cover-stats-label">{{ __('Start Tour') }}</div>
                        <div class="cover-stats-value">
                            {{ $itinerary->start_date ? $itinerary->start_date->format('M d, Y') : 'TBD' }}
                        </div>
                    </td>
                    <td class="cover-stats-cell">
                        <div class="cover-stats-label">{{ __('End Tour') }}</div>
                        <div class="cover-stats-value">
                            {{ $itinerary->end_date ? $itinerary->end_date->format('M d, Y') : 'TBD' }}
                        </div>
                    </td>
                </tr>
            </table>

            {{-- Intro Letter Box --}}
            <div class="cover-intro-box">

                <div class="cover-intro-body">
                    Dear {{ explode(' ', trim($itinerary->client_name))[0] ?: 'Traveler' }},<br><br>
                    {!! $itinerary->description ?: 'We look forward to welcoming you on this adventure.' !!}
                </div>

                <p class="cover-intro-regards">{{ __('Regards,') }}<br>
                    {{ $settings->company_name }}
                </p>

                {{-- Agent Info --}}
                <table class="cover-agent-table">
                    <tr>
                        {{-- Photo: user avatar first, company logo as fallback --}}
                        <td style="width: 56px; vertical-align: middle; padding: 10px 10px 0 0;">
                            @php
                                $agentPhoto = null;
                                // 1. Try the logged-in user's profile avatar
                                $userAvatar = auth()->user()->profile?->avatar;
                                if ($userAvatar) {
                                    $avatarPath = str_replace('\\', '/', storage_path('app/public/' . $userAvatar));
                                    if (file_exists($avatarPath)) {
                                        $agentPhoto = $avatarPath;
                                    }
                                }
                                // 2. Fallback to company logo
                                if (!$agentPhoto && $settings->company_logo) {
                                    $logoPath = str_replace('\\', '/', storage_path('app/public/' . $settings->company_logo));
                                    if (file_exists($logoPath)) {
                                        $agentPhoto = $logoPath;
                                    }
                                }
                            @endphp
                            @if($agentPhoto)
                                <img class="cover-agent-photo" src="{{ $agentPhoto }}" alt="">
                            @endif
                        </td>

                        {{-- Name & Company --}}
                        <td style="vertical-align: middle; padding: 10px 20px 0 0;">
                            <div class="cover-agent-name">{{ $settings->app_name }}</div>
                            <div class="cover-agent-company">{{ $settings->company_name }}</div>
                        </td>

                        {{-- Contact --}}
                        <td style="vertical-align: middle; padding-top: 10px;">
                            <table style="border-collapse: collapse;">
                                <tr>
                                    <td class="cover-agent-label">{{ __('Phone') }}</td>
                                    <td class="cover-agent-value">{{ $settings->phone }}</td>
                                </tr>
                                <tr>
                                    <td class="cover-agent-label">{{ __('Email') }}</td>
                                    <td class="cover-agent-value">{{ $settings->email }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

            </div>

        </div>{{-- end .cover-inner --}}

    </div>{{-- end .cover-content --}}

    {{-- ── Cover Footer (20mm) ── --}}
    <div class="cover-footer">
        <div class="cover-footer-row">
            <div class="cover-footer-left">
                <p class="cover-footer-app-name">{{ $settings->app_name }}</p>
                @if($settings->website)
                    <p class="cover-footer-website">{{ $settings->website }}</p>
                @endif
            </div>
            <div class="cover-footer-right">
                @if($settings->company_logo)
                    @php $logoPath = str_replace('\\', '/', storage_path('app/public/' . $settings->company_logo)); @endphp
                    @if(file_exists($logoPath))
                        <img class="cover-footer-logo" src="{{ $logoPath }}" alt="{{ $settings->app_name }}">
                    @endif
                @endif
            </div>
        </div>
    </div>{{-- end .cover-footer --}}


    {{-- ==================== PAGE 2: SUMMARY ==================== --}}

    <div class="summary-page">
        <div class="sum-body">

            {{-- ── "Summary" badge header ── --}}
            <div class="sum-header">
                <span class="sum-badge">{{ __('Summary') }}</span>
            </div>

            {{-- ── Tour title row with cover image ── --}}
            @php
                $coverImgPath = null;
                if ($itinerary->cover_image) {
                    $p = str_replace('\\', '/', storage_path('app/public/' . $itinerary->cover_image));
                    if (file_exists($p))
                        $coverImgPath = $p;
                }
            @endphp

            <table class="sum-title-table">
                <tr>
                    @if($coverImgPath)
                        <td style="width: 130px; vertical-align: middle;">
                            <img class="sum-cover-img" src="{{ $coverImgPath }}" alt="">
                        </td>
                    @endif
                    <td class="sum-title-cell">
                        <div class="sum-tour-title">{{ $itinerary->title }}</div>
                    </td>
                </tr>
            </table>

            {{-- ── Start / End date strip ── --}}
            <table class="sum-dates-table">
                <tr>
                    <td class="sum-dates-cell">
                        <div class="sum-date-label">{{ __('Start Tour') }}</div>
                        <div class="sum-date-value">
                            {{ $itinerary->start_date ? $itinerary->start_date->format('l, M d, Y') : 'TBD' }}
                        </div>
                    </td>
                    <td class="sum-dates-cell">
                        <div class="sum-date-label">{{ __('End Tour') }}</div>
                        <div class="sum-date-value">
                            {{ $itinerary->end_date ? $itinerary->end_date->format('l, M d, Y') : 'TBD' }}
                        </div>
                    </td>
                </tr>
            </table>

            {{-- ── Day by Day section ── --}}
            <div class="sum-dbd-title">{{ __('Day by Day') }}</div>

            {{-- Arrival info (Day 1 transports / first location) --}}
            @php
                $firstDay = $itinerary->days->first();
                $lastDay = $itinerary->days->last();
                $totalDays = $itinerary->days->count();

                $arrivalTransport = $firstDay?->transports->first();
                $arrivalText = $arrivalTransport
                    ? $arrivalTransport->name . ($arrivalTransport->pivot->time_of_day ? ', ' . $arrivalTransport->pivot->time_of_day : '')
                    : ($firstDay?->location ?: '—');

                $startDestName = $firstDay?->destination?->name ?? $firstDay?->location ?? '—';
                $endDestName = $lastDay?->destination?->name ?? $lastDay?->location ?? '—';

                $departureTransport = $lastDay?->transports->last();
                $departureText = $departureTransport
                    ? $departureTransport->name . ($departureTransport->pivot->time_of_day ? ', ' . $departureTransport->pivot->time_of_day : '')
                    : ($lastDay?->location ?: '—');

                $departureDate = $itinerary->end_date
                    ? $itinerary->end_date->addDay()->format('l, M d, Y')
                    : 'TBD';
            @endphp

            <p class="sum-info-row">
                <strong>{{ __('Arrival:') }}</strong> {{ $arrivalText }}
            </p>

            {{-- Start destination + page reference ── --}}
            <table class="sum-dest-meta">
                <tr>
                    <td class="sum-dest-meta-left">
                        <strong>{{ __('Start Destination:') }}</strong> {{ $startDestName }}
                    </td>
                    <td class="sum-dest-meta-right">{{ __('See your full itinerary on') }}<strong>Page
                            3&ndash;{{ $totalDays + 2 }}</strong>
                    </td>
                </tr>
            </table>

            {{-- Day-by-day table ── --}}
            <table class="sum-dbd-table">
                <thead class="sum-dbd-thead">
                    <tr>
                        <th style="width: 18px;"></th>
                        <th style="width: 12%;">{{ __('Days') }}</th>
                        <th style="width: 28%;">{{ __('Main Destination') }}</th>
                        <th style="width: 32%;">{{ __('Accommodation') }}</th>
                        <th>{{ __('Meal Plan') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($itinerary->days as $index => $day)
                        @php
                            $dayLabel = 'Day ' . ($index + 1);
                            $destName = $day->destination?->name ?? $day->location ?? '—';
                            $accommModel = $day->accommodation;
                            $accommName = $accommModel?->name ?? $day->accommodation ?? '—';
                            $accommType = $accommModel?->type ?? null;
                            $roomType = null; // extend if pivot has room_type

                            $mealPlan = $day->mealPlan;
                            $mealsText = $mealPlan?->name ?? $day->meals ?? '—';
                        @endphp
                        <tr class="sum-dbd-row">
                            <td class="sum-dbd-dot"><span class="sum-dot"></span></td>
                            <td><span class="sum-day-label">{{ $dayLabel }}</span></td>
                            <td>{{ $destName }}</td>
                            <td>
                                <div class="sum-accomm-name">{{ $accommName }}</div>
                                @if($accommType)
                                    <div class="sum-accomm-sub">{{ $accommType }}</div>
                                @endif
                            </td>
                            <td>{{ $mealsText }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- End destination & departure ── --}}
            <p class="sum-info-row">
                <strong>{{ __('End Destination:') }}</strong> {{ $endDestName }}
            </p>
            <p class="sum-info-row" style="margin-top: 4px;">
                <strong>{{ __('Departure:') }}</strong> {{ $departureDate }}
            </p>

        </div>{{-- end .sum-body --}}
    </div>{{-- end .summary-page --}}

    {{-- ── Summary Page Footer ── --}}
    <div class="sum-footer">
        <div class="sum-footer-row">
            <div class="sum-footer-left">
                Page 2 &nbsp;&nbsp; Ref. Number #{{ $itinerary->reference_number }} -
                {{ $itinerary->client_name ?: 'Valued Client' }}
            </div>
            <div class="sum-footer-right">
                {{ $settings->app_name }}
            </div>
        </div>
    </div>

    {{-- ==================== DAY PAGES (Page 3+) ==================== --}}

    @php $galleryImages = is_array($itinerary->gallery) ? $itinerary->gallery : []; @endphp

    @foreach($itinerary->days as $dayIndex => $day)
        @php
            $pageNum = $dayIndex + 3;
            $dayNum = $dayIndex + 1;
            $destName = $day->destination?->name ?? $day->location ?? 'Day ' . $dayNum;
            $dayDate = $day->date ? $day->date->format('l, M d, Y') : ($itinerary->start_date ? $itinerary->start_date->addDays($dayIndex)->format('l, M d, Y') : '');
            $dayDateDay = $day->date ? $day->date->format('l,') : ($itinerary->start_date ? $itinerary->start_date->addDays($dayIndex)->format('l,') : '');
            $dayDateRest = $day->date ? $day->date->format('M d, Y') : ($itinerary->start_date ? $itinerary->start_date->addDays($dayIndex)->format('M d, Y') : '');

            /* Accommodation — column & relationship share the same name;
               guard with is_object() so a plain string value doesn't cause property errors */
            $accommRaw = $day->getRelation('accommodation') ?? null;
            $accomm = ($accommRaw instanceof \App\Models\Accommodation) ? $accommRaw : null;
            $accommName = $accomm?->name ?? (is_string($accommRaw) ? $accommRaw : (is_string($day->getRawOriginal('accommodation')) ? $day->getRawOriginal('accommodation') : null));
            $accommType = $accomm?->type ?? null;
            $accommDesc = $accomm?->description ?? null;

            /* Accommodation images: featured_image → gallery array */
            $accommImages = [];
            if ($accomm) {
                if ($accomm->featured_image) {
                    $p = str_replace('\\', '/', storage_path('app/public/' . $accomm->featured_image));
                    if (file_exists($p))
                        $accommImages[] = ['path' => $p, 'caption' => $accommName];
                }
                if (!empty($accomm->gallery)) {
                    foreach ($accomm->gallery as $gi) {
                        $p = str_replace('\\', '/', storage_path('app/public/' . $gi));
                        if (file_exists($p))
                            $accommImages[] = ['path' => $p, 'caption' => $accommName];
                    }
                }
            }

            /* Day featured image */
            $dayFeatImg = null;
            if ($day->featured_image) {
                $p = str_replace('\\', '/', storage_path('app/public/' . $day->featured_image));
                if (file_exists($p))
                    $dayFeatImg = $p;
            }

            /* Meal plan */
            $mealPlan = $day->mealPlan;
            $mealsText = $mealPlan?->name ?? $day->meals ?? null;

            /* Activities */
            $activities = $day->activities;

            /* --- DYNAMIC IMAGE LOGIC --- */

            // 1. Left Column (Destinations)
            $leftTextLen = strlen(strip_tags($day->description ?? ''));
            if ($leftTextLen < 250) {
                $leftImgCount = 3;
                $leftImgHeight = '200px';
            } elseif ($leftTextLen < 600) {
                $leftImgCount = 2;
                $leftImgHeight = '200px';
            } elseif ($leftTextLen < 1000) {
                $leftImgCount = 1;
                $leftImgHeight = '180px';
            } else {
                $leftImgCount = 0;
                $leftImgHeight = '0px';
            }

            $leftAllImages = [];
            if ($dayFeatImg)
                $leftAllImages[] = ['path' => $dayFeatImg, 'caption' => $destName];
            foreach ($galleryImages as $gi) {
                $p = str_replace('\\', '/', storage_path('app/public/' . $gi));
                if (file_exists($p)) {
                    $leftAllImages[] = ['path' => $p, 'caption' => ''];
                }
            }
            $finalLeftImages = array_slice($leftAllImages, 0, $leftImgCount);

            // 2. Right Column (Accommodation & Activities)
            $rightTextLen = strlen(strip_tags($accommDesc ?? ''));
            $rightTextLen += count($activities) * 40;
            if ($mealsText)
                $rightTextLen += 40;

            if ($rightTextLen < 250) {
                $rightImgCount = 3;
                $rightImgHeight = '200px';
            } elseif ($rightTextLen < 600) {
                $rightImgCount = 2;
                $rightImgHeight = '200px';
            } elseif ($rightTextLen < 1000) {
                $rightImgCount = 1;
                $rightImgHeight = '180px';
            } else {
                $rightImgCount = 0;
                $rightImgHeight = '0px';
            }

            $finalRightImages = array_slice($accommImages, 0, $rightImgCount);
        @endphp

        {{-- ── Day Page ── --}}
        <div class="day-page">
            <div class="day-body">

                {{-- Day header --}}
                <table class="day-header-table">
                    <tr>
                        <td class="day-num-badge">
                            <span class="day-num-label">{{ __('Day') }}</span>&nbsp;{{ $dayNum }}
                        </td>
                        <td class="day-date-cell">
                            <span class="day-date-bold">{{ $dayDateDay }}</span> {{ $dayDateRest }}
                        </td>
                        <td class="day-location-cell">
                            {{ $destName }}
                        </td>
                    </tr>
                </table>

                {{-- Two-column body --}}
                <div>
                    <table class="day-cols-table">
                        <tr>

                            {{-- LEFT column --}}
                            <td class="day-col-left">

                                <div class="day-dest-title">{{ $destName }}</div>

                                @if($day->description)
                                    <div class="day-description">{!! $day->description !!}</div>
                                @endif

                                {{-- Tour / day featured images in left column (Dynamic) --}}
                                @foreach($finalLeftImages as $img)
                                    <div class="day-img-wrap">
                                        <img class="day-img-full" src="{{ $img['path'] }}" alt=""
                                            style="height: {{ $leftImgHeight }}; object-fit: cover; border-radius: 4px;">
                                        @if($img['caption'])
                                            <div class="day-img-caption">{{ $img['caption'] }}</div>
                                        @endif
                                    </div>
                                @endforeach



                            </td>

                            {{-- RIGHT column --}}
                            <td class="day-col-right">

                                {{-- Activity box --}}
                                <div class="day-activity-box">
                                    <div class="day-activity-title"><strong>{{ __('Activity') }}</strong> Day {{ $dayNum }}
                                    </div>
                                    @if($activities->isEmpty())
                                        <div class="day-activity-time">{{ __('Any time') }}</div>
                                        <div class="day-activity-item">Arrival day, {{ $destName }}</div>
                                    @else
                                        @foreach($activities as $act)
                                            @if($act->pivot->time_of_day)
                                                <div class="day-activity-time">{{ $act->pivot->time_of_day }}</div>
                                            @endif
                                            <div class="day-activity-item">{{ $act->name }}</div>
                                        @endforeach
                                    @endif
                                </div>

                                {{-- Meal plan box --}}
                                @if($mealsText)
                                    <div class="day-meal-box">
                                        <div class="day-meal-title">Meal Plan &ndash; Day {{ $dayNum }}</div>
                                        <div class="day-meal-value">{{ $mealsText }}</div>
                                    </div>
                                @endif

                                {{-- Accommodation --}}
                                @if($accommName)
                                    <hr class="day-accomm-divider">
                                    <div style="margin-bottom: 6px;">
                                        <div class="day-accomm-meta">Accommodation &nbsp;|&nbsp; Day {{ $dayNum }}</div>
                                        <div class="day-accomm-name">{{ $accommName }}</div>
                                        @if($accommType)
                                            <div class="day-accomm-type">{{ $accommType }}</div>
                                        @endif
                                    </div>

                                    @if($accommDesc)
                                        <div class="day-accomm-desc">{!! Str::limit(strip_tags($accommDesc), 200) !!}</div>
                                    @endif
                                @endif

                                {{-- Accommodation images in right column (Dynamic) --}}
                                @foreach($finalRightImages as $img)
                                    <div class="day-img-wrap">
                                        <img class="day-gallery-img" src="{{ $img['path'] }}" alt=""
                                            style="height: {{ $rightImgHeight }}; object-fit: cover; border-radius: 4px;">
                                        @if($img['caption'])
                                            <div class="day-img-caption">{{ $img['caption'] }}</div>
                                        @endif
                                    </div>
                                @endforeach

                            </td>

                        </tr>
                    </table>
                </div>{{-- end Two-column body div --}}

            </div>{{-- end .day-body --}}

            {{-- Day page footer --}}
            <div class="day-footer">
                <div class="day-footer-row">
                    <div class="day-footer-left">
                        Page {{ $pageNum }} &nbsp;&nbsp; Ref. Number #{{ $itinerary->reference_number }} -
                        {{ $itinerary->client_name ?: 'Valued Client' }}
                    </div>
                    <div class="day-footer-right">
                        {{ $settings->app_name }}
                    </div>
                </div>
            </div>

        </div>{{-- end .day-page --}}

    @endforeach

    {{-- ==================== PRICING PAGE 1 ==================== --}}
    @php
        $pricingPage1Num = isset($pageNum) ? $pageNum + 1 : 1;
        $guestCount = ($itinerary->adults ?? 0) + ($itinerary->children ?? 0);
        $currencySymbol = \App\Models\Currency::where('code', $itinerary->currency)->first()?->symbol ?? $itinerary->currency;
        $totalFormatted = number_format($itinerary->total_price ?? 0, 2);
    @endphp
    <div class="day-page">
        <div class="day-body">
            <table class="day-header-table">
                <tr>
                    <td class="day-num-badge">
                        <span class="day-num-label" style="font-size:12px;">{{ __('Pricing') }}</span>
                    </td>
                    <td class="day-location-cell" style="text-align: right; padding-right: 20px;">
                        <strong>Our offer for {{ $itinerary->client_name ?: 'Valued Client' }}</strong>
                    </td>
                </tr>
            </table>

            <table class="pricing-table-top">
                <tr>
                    <td>
                        <div class="pricing-meta-label">{{ __('Travelers') }}</div>
                        <div class="pricing-meta-value">{{ $guestCount }}x Guest</div>
                    </td>
                    <td>
                        <div class="pricing-meta-label">{{ __('Start Tour') }}</div>
                        <div class="pricing-meta-value">
                            {{ $itinerary->start_date ? $itinerary->start_date->format('M d, Y') : 'N/A' }}
                        </div>
                    </td>
                    <td>
                        <div class="pricing-meta-label">{{ __('End Tour') }}</div>
                        <div class="pricing-meta-value">
                            {{ $itinerary->end_date ? $itinerary->end_date->format('M d, Y') : 'N/A' }}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="pricing-meta-label">{{ __('Tour Length') }}</div>
                        <div class="pricing-meta-value">{{ $itinerary->duration_days ?? 0 }} Days</div>
                    </td>
                    <td>
                        <div class="pricing-meta-label">{{ __('Arrival') }}</div>
                        <div class="pricing-meta-value">
                            {{ $itinerary->start_date ? $itinerary->start_date->format('M d, Y') : 'N/A' }}
                        </div>
                    </td>
                    <td>
                        <div class="pricing-meta-label">{{ __('Departure') }}</div>
                        <div class="pricing-meta-value">
                            {{ $itinerary->end_date ? $itinerary->end_date->format('M d, Y') : 'N/A' }}
                        </div>
                    </td>
                </tr>
            </table>

            <table class="pricing-inc-exc-table">
                <tr>
                    <td>
                        <div class="pricing-inc-exc-title">{{ __('Included') }}</div>
                        <ul class="pricing-list">
                            @if(is_array($itinerary->included) && count($itinerary->included) > 0)
                                @foreach($itinerary->included as $inc)
                                    <li>{{ is_array($inc) ? ($inc['name'] ?? '') : $inc }}</li>
                                @endforeach
                            @else
                                <li>{{ __('As per itinerary') }}</li>
                            @endif
                        </ul>
                    </td>
                    <td>
                        <div class="pricing-inc-exc-title">{{ __('Excluded') }}</div>
                        <ul class="pricing-list">
                            @if(is_array($itinerary->excluded) && count($itinerary->excluded) > 0)
                                @foreach($itinerary->excluded as $exc)
                                    <li>{{ is_array($exc) ? ($exc['name'] ?? '') : $exc }}</li>
                                @endforeach
                            @else
                                <li>{{ __('International flights') }}</li>
                                <li>{{ __('Personal items') }}</li>
                            @endif
                        </ul>
                    </td>
                </tr>
            </table>

            <div class="pricing-section-title">{{ __('Breakdown of Costs') }}</div>
            <table class="pricing-breakdown-table">
                <tr>
                    <td>{{ $itinerary->adults ?? 0 }}x Adults</td>
                    <td style="text-align: right;">
                        {{ $currencySymbol }}{{ number_format(($itinerary->total_price ?? 0), 2) }}
                    </td>
                    <td style="text-align: right;">
                        {{ $currencySymbol }}{{ number_format(($itinerary->total_price ?? 0), 2) }}
                    </td>
                </tr>
                <tr class="pricing-breakdown-total">
                    <td colspan="2" style="text-align: right;">Total in {{ $itinerary->currency ?? 'USD' }}</td>
                    <td style="text-align: right;">{{ $currencySymbol }}{{ $totalFormatted }}</td>
                </tr>
            </table>
            <div class="pricing-btn-container">
                <a href="{{ route('client.dashboard') }}" class="btn-booking">{{ __('Confirm Booking') }}</a>
            </div>

        </div>
        <div class="day-footer">
            <div class="day-footer-row">
                <div class="day-footer-left">
                    Page {{ $pricingPage1Num }} &nbsp;&nbsp; Ref. Number #{{ $itinerary->reference_number }} -
                    {{ $itinerary->client_name ?: 'Valued Client' }}
                </div>
                <div class="day-footer-right">
                    {{ $settings->app_name }}
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== PRICING PAGE 2 ==================== --}}
    @php
        $pricingPage2Num = $pricingPage1Num + 1;
    @endphp
    <div class="day-page">
        <div class="day-body">
            <table class="day-header-table">
                <tr>
                    <td class="day-num-badge">
                        <span class="day-num-label" style="font-size:12px;">{{ __('Pricing') }}</span>
                    </td>
                    <td class="day-location-cell" style="text-align: right; padding-right: 20px;">
                        <strong>Our offer for {{ $itinerary->client_name ?: 'Valued Client' }}</strong>
                    </td>
                </tr>
            </table>

            <div class="payment-box">
                <div class="payment-title">{{ __('Our Payment Terms') }}</div>
                <div class="payment-subtitle">{{ __('Payment') }}</div>
                <div class="payment-text">
                    {!! nl2br(e($itinerary->payment_terms ?? 'To confirm the booking of your trip, a deposit of 30% of the total package price must be paid after confirming/agreeing with the itinerary. 70% of balance payments must be paid two weeks before the start of a trip. However, if you prefer, you can pay the full amount upfront as well.')) !!}
                </div>

                <div class="payment-subtitle">{{ __('Bank Details') }}</div>
                <div class="payment-text">
                    Account Name: {{ $settings->bank_account_name ?? 'N/A' }}<br>
                    Account Number: {{ $settings->bank_account_number ?? 'N/A' }}<br>
                    Bank Name: {{ $settings->bank_name ?? 'N/A' }}<br>
                    Branch: {{ $settings->bank_branch ?? 'N/A' }}<br>
                    Swift Code: {{ $settings->bank_swift ?? 'N/A' }}
                </div>

                <div class="payment-subtitle">{{ __('Payment Details') }}</div>
                <div class="payment-text">
                    {{ __('Please note the above amount is Net.') }}<br>{{ __('Bank Charges should not be deducted when making payments.') }}<br><br>
                    Please keep in mind that international bank charges are subject to additional charges that are
                    deducted from the amount sent. These charges can vary from $ 30 to $ 50, so please make sure this
                    amount is covered on your side when sending the payment so that full payment is received.<br>
                    <em>{{ __('Please send us a copy of the swift transfer proof of payment and request your receipt.') }}</em><br><br>
                    <strong>{{ __('NB: Make all cheques/cash/transfers payable to the Company Name only') }}<br>{{ __('Thank you for your business!') }}</strong>
                </div>
            </div>

            <div class="pricing-section-title">{{ __('Optional, not included') }}</div>
            <table class="optional-table">
                <tr>
                    <th>{{ __('Option') }}</th>
                    <th>{{ __('Price') }}</th>
                </tr>
                <tr>
                    <td>{{ __('Hot Air Balloon') }}</td>
                    <td>$550.00 Per person</td>
                </tr>
            </table>
        </div>
        <div class="day-footer">
            <div class="day-footer-row">
                <div class="day-footer-left">
                    Page {{ $pricingPage2Num }} &nbsp;&nbsp; Ref. Number #{{ $itinerary->reference_number }} -
                    {{ $itinerary->client_name ?: 'Valued Client' }}
                </div>
                <div class="day-footer-right">
                    {{ $settings->app_name }}
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== ABOUT US PAGE ==================== --}}
    @php
        $aboutPageNum = isset($pricingPage2Num) ? $pricingPage2Num + 1 : (isset($pageNum) ? $pageNum + 1 : 1);
    @endphp
    <div class="day-page">
        <div class="day-body">
            <table class="day-header-table">
                <tr>
                    <td class="day-num-badge" style="background:#f5a623; padding: 8px 16px;">
                        <span class="day-num-label"
                            style="font-size:12px; font-weight:bold; color:#000;">{{ __('About Us') }}</span>
                    </td>
                    <td class="day-location-cell">
                        &nbsp;
                    </td>
                </tr>
            </table>

            <div style="margin-top: 30px;">
                <div class="about-title">{{ $settings->app_name ?? ' ' }}</div>
                <div class="about-text">
                    Welcome to
                    {{ $settings->app_name ?? ' ' }}<br>{{ __('Your one-stop shop for an amazing trip to Tanzania.') }}<br>
                    We are a recognized tour company with a plethora of travel industry expertise situated in Arusha,
                    Tanzania. That is why we are here to handle everything from accommodation and sightseeing to safari
                    excursions and mountain climbing expeditions. We offer a broad variety of customized vacation
                    packages tailored to your unique needs and budget in Mount Kilimanjaro hike, Tarangire, Serengeti
                    National Park, Ngorongoro Crater, etc.
                </div>

                <div class="contact-box" style="margin-top: 30px; margin-bottom: 30px;">
                    <div class="contact-box-title">{{ __('Contact Us') }}</div>
                    <table class="contact-table">
                        <tr>
                            <td>{{ __('Address') }}</td>
                            <td>{{ $settings->company_address ?? 'NIC Building First Floor, Clock Tower 23100, Arusha, Boma Road' }}
                            </td>
                        </tr>
                        <tr>
                            <td>{{ __('Country') }}</td>
                            <td>{{ $settings->company_country ?? 'Tanzania' }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('WhatsApp') }}</td>
                            <td>{{ $settings->company_phone ?? ' ' }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Email') }}</td>
                            <td>{{ $settings->company_email ?? ' ' }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Website') }}</td>
                            <td>{{ $settings->company_website ?? ' ' }}</td>
                        </tr>
                    </table>

                    <div class="contact-extra">
                        {{ __('Contact person:') }}<strong>{{ $settings->company_phone ?? ' ' }}</strong>
                        <br><br>
                        <strong>{{ __('Member of:') }}</strong><br>
                        &bull; Tanzania Tourist Board
                    </div>
                </div>

                @if($settings->company_logo)
                    @php $logoPath = str_replace('\\', '/', storage_path('app/public/' . $settings->company_logo)); @endphp
                    @if(file_exists($logoPath))
                        <div style="text-align: center; margin-top: 20px;">
                            <img class="about-logo" src="{{ $logoPath }}" alt="{{ $settings->app_name }}">
                        </div>
                    @endif
                @endif
            </div>

        </div>
        <div class="day-footer">
            <div class="day-footer-row">
                <div class="day-footer-left">
                    Page {{ $aboutPageNum }} &nbsp;&nbsp; Ref. Number #{{ $itinerary->reference_number }} -
                    {{ $itinerary->client_name ?: 'Valued Client' }}
                </div>
                <div class="day-footer-right">
                    {{ $settings->app_name }}
                </div>
            </div>
        </div>
    </div>

</body>

</html>