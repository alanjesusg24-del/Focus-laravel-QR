<?php

/**
 * ============================================
 * CETAM - Configuración de Iconos
 * ============================================
 *
 * Font Awesome 5.3.1 Free Solid Icons
 * Catálogo estandarizado de iconos institucionales
 *
 * @file        icons.php
 * @author      CETAM Dev Team
 * @created     2025-11-20
 * @updated     2025-11-26
 * @version     2.1.0 (Compatible con FA 5.3.1)
 *
 * Uso: <x-icon name="nav.home" />
 *      <x-icon name="action.edit" class="me-2" />
 *
 * ============================================
 */

return [
    // Navigation & Interface
    'nav.home' => 'fas fa-home',
    'nav.dashboard' => 'fas fa-tachometer-alt',
    'nav.menu' => 'fas fa-bars',
    'nav.back' => 'fas fa-arrow-left',
    'nav.forward' => 'fas fa-arrow-right',
    'nav.up' => 'fas fa-arrow-up',
    'nav.down' => 'fas fa-arrow-down',
    'nav.close' => 'fas fa-times',
    'nav.external_link' => 'fas fa-external-link-alt',

    // User & Authentication
    'user.profile' => 'fas fa-user',
    'user.avatar' => 'fas fa-user-circle',
    'user.add' => 'fas fa-user-plus',
    'user.remove' => 'fas fa-user-minus',
    'user.list' => 'fas fa-users',
    'user.admin' => 'fas fa-user-tie',
    'auth.login' => 'fas fa-sign-in-alt',
    'auth.logout' => 'fas fa-sign-out-alt',

    // Common Actions
    'action.create' => 'fas fa-plus',
    'action.edit' => 'fas fa-edit',
    'action.delete' => 'fas fa-trash',
    'action.view' => 'fas fa-eye',
    'action.save' => 'fas fa-save',
    'action.cancel' => 'fas fa-times',
    'action.send' => 'fas fa-paper-plane',
    'action.download' => 'fas fa-download',
    'action.upload' => 'fas fa-upload',
    'action.search' => 'fas fa-search',
    'action.refresh' => 'fas fa-sync',
    'action.filter' => 'fas fa-filter',
    'action.sort' => 'fas fa-sort',
    'action.sort_up' => 'fas fa-sort-up',
    'action.sort_down' => 'fas fa-sort-down',
    'action.more' => 'fas fa-ellipsis-h',

    // States
    'state.success' => 'fas fa-check-circle',
    'state.error' => 'fas fa-times-circle',
    'state.warning' => 'fas fa-exclamation-triangle',
    'state.info' => 'fas fa-info-circle',
    'state.pending' => 'fas fa-clock',
    'state.in_progress' => 'fas fa-spinner',
    'state.sync' => 'fas fa-sync',
    'state.approved' => 'fas fa-thumbs-up',
    'state.rejected' => 'fas fa-thumbs-down',
    'state.canceled' => 'fas fa-ban',
    'state.finished' => 'fas fa-flag-checkered',
    'state.started' => 'fas fa-play',

    // Files & Documents
    'file.generic' => 'fas fa-file',
    'file.pdf' => 'fas fa-file-pdf',
    'file.word' => 'fas fa-file-word',
    'file.excel' => 'fas fa-file-excel',
    'file.image' => 'fas fa-file-image',
    'file.csv' => 'fas fa-file-csv',
    'file.attachment' => 'fas fa-paperclip',
    'folder.closed' => 'fas fa-folder',
    'folder.open' => 'fas fa-folder-open',

    // Processes & Settings
    'process.generic' => 'fas fa-cogs',
    'process.approval' => 'fas fa-check-square',
    'process.step' => 'fas fa-shoe-prints',
    'process.docs' => 'fas fa-file-signature',
    'process.branch' => 'fas fa-code-branch',
    'config.settings' => 'fas fa-cog',

    // Notifications & Communication
    'notif.bell' => 'fas fa-bell',
    'notif.off' => 'fas fa-bell-slash',
    'msg.inbox' => 'fas fa-inbox',
    'msg.chat' => 'fas fa-comments',
    'msg.email' => 'fas fa-envelope',
    'msg.phone' => 'fas fa-phone',
    'support.help' => 'fas fa-question-circle',
    'support.contact' => 'fas fa-life-ring',

    // Reports & Analytics
    'report.line' => 'fas fa-chart-line',
    'report.bar' => 'fas fa-chart-bar',
    'report.pie' => 'fas fa-chart-pie',
    'report.print' => 'fas fa-print',
    'report.download' => 'fas fa-file-download',

    // Security & Access
    'access.lock' => 'fas fa-lock',
    'access.unlock' => 'fas fa-lock-open',
    'access.key' => 'fas fa-key',
    'access.shield' => 'fas fa-shield-alt',

    // Lists & Filters
    'list.unordered' => 'fas fa-list-ul',
    'list.ordered' => 'fas fa-list-ol',
    'list.checklist' => 'fas fa-tasks',

    // Frequent Domains
    'geo.location' => 'fas fa-map-marker-alt',
    'money.currency' => 'fas fa-dollar-sign',
    'money.coins' => 'fas fa-coins',
    'money.card' => 'fas fa-credit-card',
    'money.invoice' => 'fas fa-file-invoice-dollar',
    'store.inventory' => 'fas fa-boxes',

    // === EXTENSIONES ESPECÍFICAS DEL PROYECTO ORDER QR ===

    // Orders & QR System
    'order.list' => 'fas fa-clipboard-list',
    'order.new' => 'fas fa-plus-circle',
    'order.qr' => 'fas fa-qrcode',
    'order.scan' => 'fas fa-barcode',
    'order.ready' => 'fas fa-check-circle',
    'order.delivered' => 'fas fa-shipping-fast',
    'order.pending' => 'fas fa-clock',
    'order.cancelled' => 'fas fa-times-circle',

    // Business & Plans
    'business.store' => 'fas fa-store',
    'business.list' => 'fas fa-building',
    'business.profile' => 'fas fa-store-alt',
    'plan.subscription' => 'fas fa-crown',
    'plan.pricing' => 'fas fa-tags',

    // Payments
    'payment.card' => 'fas fa-credit-card',
    'payment.history' => 'fas fa-receipt',
    'payment.success' => 'fas fa-check-circle',
    'payment.pending' => 'fas fa-hourglass-half',
    'payment.failed' => 'fas fa-exclamation-circle',

    // Mobile & Notifications
    'mobile.app' => 'fas fa-mobile-alt',
    'mobile.user' => 'fas fa-mobile',
    'notification.push' => 'fas fa-bell',
    'notification.alert' => 'fas fa-bell',

    // Support & Tickets
    'ticket.new' => 'fas fa-ticket-alt',
    'ticket.open' => 'fas fa-ticket-alt',
    'ticket.resolved' => 'fas fa-check-circle',
    'support.ticket' => 'fas fa-headset',

    // Stats & Dashboard
    'stats.chart' => 'fas fa-chart-area',
    'stats.trending_up' => 'fas fa-arrow-up',
    'stats.trending_down' => 'fas fa-arrow-down',
    'stats.calendar' => 'fas fa-calendar-alt',

    // Time
    'time.clock' => 'fas fa-clock',
    'time.calendar' => 'fas fa-calendar',
    'time.history' => 'fas fa-history',

    // Misc
    'misc.star' => 'fas fa-star',
    'misc.heart' => 'fas fa-heart',
    'misc.bookmark' => 'fas fa-bookmark',
    'misc.flag' => 'fas fa-flag',
    'misc.tag' => 'fas fa-tag',

    // === ALIASES CORTOS (para compatibilidad) ===
    'error' => 'fas fa-times-circle',
    'check' => 'fas fa-check-circle',
    'checkCircle' => 'fas fa-check-circle',
    'warning' => 'fas fa-exclamation-triangle',
    'store' => 'fas fa-store',
    'idCard' => 'fas fa-id-card',
    'phone' => 'fas fa-phone',
    'email' => 'fas fa-envelope',
    'lock' => 'fas fa-lock',
    'user' => 'fas fa-user',
    'map' => 'fas fa-map-marked-alt',
    'calendar' => 'fas fa-calendar',
    'building' => 'fas fa-building',
    'home' => 'fas fa-home',
    'dashboard' => 'fas fa-tachometer-alt',
    'orders' => 'fas fa-clipboard-list',
    'payments' => 'fas fa-credit-card',
    'settings' => 'fas fa-cog',
    'logout' => 'fas fa-sign-out-alt',
    'save' => 'fas fa-save',
    'edit' => 'fas fa-edit',
    'delete' => 'fas fa-trash',
    'view' => 'fas fa-eye',
    'qr' => 'fas fa-qrcode',
    'back' => 'fas fa-arrow-left',
    'forward' => 'fas fa-arrow-right',
    'arrowRight' => 'fas fa-arrow-right',
    'close' => 'fas fa-times',
    'listCheck' => 'fas fa-tasks',
    'creditCard' => 'fas fa-credit-card',
    'comments' => 'fas fa-comments',
];
