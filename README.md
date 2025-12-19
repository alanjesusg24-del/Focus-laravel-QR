
```
Focus-laravel-QR
├─ .claude-instructions
├─ .editorconfig
├─ .nvmrc
├─ .phpunit.cache
│  └─ test-results
├─ .railwayignore
├─ .styleci.yml
├─ agregar_ubicaciones_prueba.php
├─ app
│  ├─ Console
│  │  ├─ Commands
│  │  │  ├─ CheckExpiredPayments.php
│  │  │  ├─ CleanExpiredOrders.php
│  │  │  ├─ GenerateSystemReport.php
│  │  │  ├─ SendOrderRealerts.php
│  │  │  ├─ SendPaymentReminders.php
│  │  │  └─ TestMercadoPago.php
│  │  └─ Kernel.php
│  ├─ Exceptions
│  │  └─ Handler.php
│  ├─ Http
│  │  ├─ Controllers
│  │  │  ├─ Api
│  │  │  │  ├─ BusinessApiController.php
│  │  │  │  ├─ ChatApiController.php
│  │  │  │  ├─ MobileAuthController.php
│  │  │  │  └─ V1
│  │  │  │     ├─ Auth
│  │  │  │     │  └─ AuthController.php
│  │  │  │     ├─ BusinessLocationController.php
│  │  │  │     └─ MobileController.php
│  │  │  ├─ Auth
│  │  │  │  └─ AuthController.php
│  │  │  ├─ BusinessController.php
│  │  │  ├─ ChatController.php
│  │  │  ├─ Controller.php
│  │  │  ├─ CS
│  │  │  │  ├─ DashboardController.php
│  │  │  │  └─ OrderController.php
│  │  │  ├─ DashboardController.php
│  │  │  ├─ MercadoPagoWebhookController.php
│  │  │  ├─ OrderController.php
│  │  │  ├─ PaymentController.php
│  │  │  ├─ SuperAdmin
│  │  │  │  ├─ AuthController.php
│  │  │  │  ├─ BusinessManagementController.php
│  │  │  │  ├─ DashboardController.php
│  │  │  │  ├─ GlobalOrderController.php
│  │  │  │  ├─ PaymentManagementController.php
│  │  │  │  ├─ PlanManagementController.php
│  │  │  │  ├─ ProfileController.php
│  │  │  │  ├─ ReportController.php
│  │  │  │  └─ TicketManagementController.php
│  │  │  └─ SupportTicketController.php
│  │  ├─ Kernel.php
│  │  ├─ Middleware
│  │  │  ├─ Authenticate.php
│  │  │  ├─ CheckActiveSubscription.php
│  │  │  ├─ CheckBusinessPayment.php
│  │  │  ├─ EncryptCookies.php
│  │  │  ├─ MobileDeviceMiddleware.php
│  │  │  ├─ OptionalSanctumAuth.php
│  │  │  ├─ PreventRequestsDuringMaintenance.php
│  │  │  ├─ RedirectIfAuthenticated.php
│  │  │  ├─ TrimStrings.php
│  │  │  ├─ TrustHosts.php
│  │  │  ├─ TrustProxies.php
│  │  │  └─ VerifyCsrfToken.php
│  │  └─ Requests
│  │     ├─ CreateBusinessRequest.php
│  │     ├─ CreateOrderRequest.php
│  │     ├─ CreatePaymentRequest.php
│  │     ├─ CreateSupportTicketRequest.php
│  │     ├─ NearbyBusinessesRequest.php
│  │     ├─ SearchBusinessesRequest.php
│  │     ├─ UpdateBusinessRequest.php
│  │     └─ UpdateOrderRequest.php
│  ├─ Livewire
│  │  └─ Auth
│  │     ├─ Login.php
│  │     ├─ Register.php
│  │     └─ RegisterWizard.php
│  ├─ Models
│  │  ├─ Business.php
│  │  ├─ ChatMessage.php
│  │  ├─ MobileDevice.php
│  │  ├─ MobileUser.php
│  │  ├─ Notification.php
│  │  ├─ Order.php
│  │  ├─ OrderItem.php
│  │  ├─ OrderRealert.php
│  │  ├─ OrderStatusHistory.php
│  │  ├─ Payment.php
│  │  ├─ Plan.php
│  │  ├─ SuperAdmin.php
│  │  ├─ SupportTicket.php
│  │  └─ User.php
│  ├─ Notifications
│  │  └─ ResetPassword.php
│  ├─ Policies
│  │  ├─ OrderPolicy.php
│  │  └─ SupportTicketPolicy.php
│  ├─ Providers
│  │  ├─ AppServiceProvider.php
│  │  ├─ AuthServiceProvider.php
│  │  ├─ BroadcastServiceProvider.php
│  │  ├─ EventServiceProvider.php
│  │  └─ RouteServiceProvider.php
│  ├─ Services
│  │  ├─ MercadoPagoService.php
│  │  ├─ NotificationService.php
│  │  ├─ OrderService.php
│  │  ├─ PaymentService.php
│  │  ├─ PushNotificationService.php
│  │  └─ QrCodeService.php
│  └─ View
│     └─ Components
│        ├─ CS
│        │  └─ Alert.php
│        ├─ Icon.php
│        └─ Layouts
│           └─ Base.php
├─ artisan
├─ bootstrap
│  ├─ app.php
│  └─ cache
│     ├─ packages.php
│     └─ services.php
├─ composer.json
├─ composer.lock
├─ config
│  ├─ app.php
│  ├─ auth.php
│  ├─ broadcasting.php
│  ├─ cache.php
│  ├─ cetam.cs.php
│  ├─ cors.php
│  ├─ database.php
│  ├─ filesystems.php
│  ├─ hashing.php
│  ├─ icons.php
│  ├─ livewire.php
│  ├─ logging.php
│  ├─ mail.php
│  ├─ proj.php
│  ├─ queue.php
│  ├─ sanctum.php
│  ├─ services.php
│  ├─ session.php
│  └─ view.php
├─ controllers-inventory.txt
├─ database
│  ├─ factories
│  │  └─ UserFactory.php
│  ├─ migrations
│  │  ├─ 2014_10_12_000000_create_users_table.php
│  │  ├─ 2014_10_12_100000_create_password_resets_table.php
│  │  ├─ 2019_08_19_000000_create_failed_jobs_table.php
│  │  ├─ 2019_12_14_000001_create_personal_access_tokens_table.php
│  │  ├─ 2025_11_03_204427_create_plans_table.php
│  │  ├─ 2025_11_03_204454_create_super_admins_table.php
│  │  ├─ 2025_11_03_204519_create_businesses_table.php
│  │  ├─ 2025_11_03_204548_create_orders_table.php
│  │  ├─ 2025_11_03_204618_create_mobile_devices_table.php
│  │  ├─ 2025_11_03_204645_create_notifications_table.php
│  │  ├─ 2025_11_03_204715_create_payments_table.php
│  │  ├─ 2025_11_03_204745_create_support_tickets_table.php
│  │  ├─ 2025_11_04_155034_add_modular_pricing_to_plans_and_businesses.php
│  │  ├─ 2025_11_06_091909_create_mobile_users_table.php
│  │  ├─ 2025_11_06_091917_create_order_items_table.php
│  │  ├─ 2025_11_06_091917_create_order_status_history_table.php
│  │  ├─ 2025_11_06_092001_add_mobile_fields_to_orders_table.php
│  │  ├─ 2025_11_14_151759_add_location_and_photo_to_businesses_table.php
│  │  ├─ 2025_11_14_153530_add_soft_deletes_to_orders_table.php
│  │  ├─ 2025_11_19_082110_add_chat_and_realerts_to_plans_table.php
│  │  ├─ 2025_11_19_082426_create_order_realerts_table.php
│  │  ├─ 2025_11_19_083936_add_realert_time_fields_to_plans_table.php
│  │  ├─ 2025_11_19_090028_add_photo_to_businesses_table.php
│  │  ├─ 2025_11_19_091635_remove_unused_fields_from_businesses_table.php
│  │  ├─ 2025_11_19_134413_add_attachment_url_to_support_tickets_table.php
│  │  ├─ 2025_11_19_142205_add_response_attachment_url_to_support_tickets_table.php
│  │  ├─ 2025_11_19_180843_create_chat_messages_table.php
│  │  ├─ 2025_11_19_230256_add_google_fields_to_users_table.php
│  │  ├─ 2025_11_19_230744_add_name_column_to_users_table.php
│  │  ├─ 2025_11_19_232557_add_user_id_to_orders_table.php
│  │  ├─ 2025_11_20_100642_add_mercadopago_fields_to_payments_and_businesses_table.php
│  │  ├─ 2025_11_26_144109_add_location_fields_to_businesses_table.php
│  │  ├─ 2025_11_27_140452_add_auth_fields_to_mobile_users_table.php
│  │  ├─ 2025_12_09_154621_add_business_folio_to_orders_table.php
│  │  ├─ 2025_12_09_160835_remove_unique_constraint_from_business_folio.php
│  │  ├─ 2025_12_10_143058_remove_location_description_from_businesses_table.php
│  │  └─ 2025_12_17_003325_add_logo_url_to_businesses_table.php
│  └─ seeders
│     ├─ BusinessSeeder.php
│     ├─ DatabaseSeeder.php
│     ├─ DemoOrdersSeeder.php
│     ├─ MobileAppSeeder.php
│     ├─ OrderSeeder.php
│     ├─ PaymentSeeder.php
│     ├─ PlanSeeder.php
│     ├─ SuperAdminSeeder.php
│     ├─ SupportTicketSeeder.php
│     └─ UserSeeder.php
├─ estructura.txt
├─ models-inventory.txt
├─ nixpacks.toml
├─ package-lock.json
├─ package.json
├─ phpunit.xml
├─ Procfile
├─ project-analysis.txt
├─ public
│  ├─ .htaccess
│  ├─ assets
│  │  ├─ img
│  │  │  ├─ brand
│  │  │  │  ├─ dark.svg
│  │  │  │  └─ light.svg
│  │  │  ├─ favicon
│  │  │  │  ├─ android-chrome-192x192.png
│  │  │  │  ├─ android-chrome-512x512.png
│  │  │  │  ├─ apple-touch-icon.png
│  │  │  │  ├─ browserconfig.xml
│  │  │  │  ├─ favicon-16x16.png
│  │  │  │  ├─ favicon-32x32.png
│  │  │  │  ├─ favicon.ico
│  │  │  │  ├─ manifest.json
│  │  │  │  ├─ mstile-150x150.png
│  │  │  │  ├─ safari-pinned-tab.svg
│  │  │  │  └─ site.webmanifest
│  │  │  ├─ focus-icon.svg
│  │  │  ├─ icons
│  │  │  │  ├─ github.svg
│  │  │  │  ├─ google-tag-manager.svg
│  │  │  │  └─ google_analytics.svg
│  │  │  ├─ illustrations
│  │  │  │  ├─ 404.svg
│  │  │  │  ├─ 500.svg
│  │  │  │  └─ signin.svg
│  │  │  ├─ marker.svg
│  │  │  ├─ paypal-logo.svg
│  │  │  ├─ technologies
│  │  │  │  ├─ bootstrap-5-logo.svg
│  │  │  │  ├─ react-logo-primary.svg
│  │  │  │  ├─ react-logo.svg
│  │  │  │  └─ vuejs-logo.svg
│  │  │  ├─ themesberg-logo-alt.svg
│  │  │  └─ themesberg.svg
│  │  └─ js
│  │     ├─ bootstrap.min.js
│  │     ├─ chartist-plugin-tooltip.min.js
│  │     ├─ chartist.min.js
│  │     ├─ datepicker.min.js
│  │     ├─ demo.js
│  │     ├─ nouislider.min.js
│  │     ├─ on-screen.umd.min.js
│  │     ├─ popper.min.js
│  │     ├─ simplebar.min.js
│  │     ├─ smooth-scroll.polyfills.min.js
│  │     ├─ sweetalert2.all.min.js
│  │     └─ volt.js
│  ├─ css
│  │  ├─ all.min.css
│  │  ├─ app.css
│  │  ├─ cetam-colors.css
│  │  ├─ cetam-sidebar.css
│  │  └─ volt.css
│  ├─ favicon.ico
│  ├─ index.php
│  ├─ js
│  │  ├─ app.js
│  │  └─ app.js.LICENSE.txt
│  ├─ mix-manifest.json
│  ├─ robots.txt
│  └─ web.config
├─ resources
│  ├─ css
│  │  └─ app.css
│  ├─ js
│  │  ├─ app.js
│  │  ├─ bootstrap.js
│  │  └─ notifications.js
│  ├─ lang
│  │  └─ en
│  │     ├─ auth.php
│  │     ├─ pagination.php
│  │     ├─ passwords.php
│  │     └─ validation.php
│  ├─ sass
│  │  ├─ custom
│  │  │  ├─ _components.scss
│  │  │  ├─ _custom.scss
│  │  │  └─ _variables.scss
│  │  ├─ volt
│  │  │  ├─ components
│  │  │  │  ├─ _accordions.scss
│  │  │  │  ├─ _alerts.scss
│  │  │  │  ├─ _animations.scss
│  │  │  │  ├─ _avatars.scss
│  │  │  │  ├─ _badge.scss
│  │  │  │  ├─ _body.scss
│  │  │  │  ├─ _breadcrumb.scss
│  │  │  │  ├─ _buttons.scss
│  │  │  │  ├─ _card.scss
│  │  │  │  ├─ _carousel.scss
│  │  │  │  ├─ _charts.scss
│  │  │  │  ├─ _close.scss
│  │  │  │  ├─ _custom-forms.scss
│  │  │  │  ├─ _datepicker.scss
│  │  │  │  ├─ _dropdown.scss
│  │  │  │  ├─ _icons.scss
│  │  │  │  ├─ _images.scss
│  │  │  │  ├─ _list-group.scss
│  │  │  │  ├─ _modal.scss
│  │  │  │  ├─ _nav.scss
│  │  │  │  ├─ _pagination.scss
│  │  │  │  ├─ _popover.scss
│  │  │  │  ├─ _progress.scss
│  │  │  │  ├─ _scrollbar.scss
│  │  │  │  ├─ _shapes.scss
│  │  │  │  ├─ _steps.scss
│  │  │  │  ├─ _tables.scss
│  │  │  │  ├─ _timelines.scss
│  │  │  │  ├─ _tooltip.scss
│  │  │  │  └─ _type.scss
│  │  │  ├─ forms
│  │  │  │  ├─ _form-check.scss
│  │  │  │  ├─ _form-control.scss
│  │  │  │  ├─ _form-select.scss
│  │  │  │  └─ _input-group.scss
│  │  │  ├─ layout
│  │  │  │  ├─ _footer.scss
│  │  │  │  ├─ _navbar.scss
│  │  │  │  ├─ _section.scss
│  │  │  │  ├─ _sidebar.scss
│  │  │  │  └─ _sidenav.scss
│  │  │  ├─ mixins
│  │  │  │  ├─ _animations.scss
│  │  │  │  ├─ _background-variant.scss
│  │  │  │  ├─ _icon.scss
│  │  │  │  ├─ _modals.scss
│  │  │  │  ├─ _popover.scss
│  │  │  │  ├─ _transform.scss
│  │  │  │  └─ _utilities.scss
│  │  │  ├─ _components.scss
│  │  │  ├─ _forms.scss
│  │  │  ├─ _functions.scss
│  │  │  ├─ _layout.scss
│  │  │  ├─ _mixins.scss
│  │  │  ├─ _utilities.scss
│  │  │  ├─ _variables.scss
│  │  │  └─ _vendor.scss
│  │  └─ volt.scss
│  └─ views
│     ├─ 404.blade.php
│     ├─ 500.blade.php
│     ├─ auth
│     │  └─ login.blade.php
│     ├─ bootstrap-tables.blade.php
│     ├─ business
│     │  ├─ change-password.blade.php
│     │  ├─ edit.blade.php
│     │  ├─ profile.blade.php
│     │  └─ register.blade.php
│     ├─ chat
│     │  └─ index.blade.php
│     ├─ components
│     │  ├─ alert.blade.php
│     │  ├─ button.blade.php
│     │  ├─ buttons.blade.php
│     │  ├─ chat-widget.blade.php
│     │  ├─ cs
│     │  │  └─ alert.blade.php
│     │  ├─ forms.blade.php
│     │  ├─ icon.blade.php
│     │  ├─ legal-content
│     │  │  ├─ privacy.blade.php
│     │  │  └─ terms.blade.php
│     │  ├─ legal-modals.blade.php
│     │  ├─ modal.blade.php
│     │  ├─ modals.blade.php
│     │  ├─ notifications.blade.php
│     │  └─ typography.blade.php
│     ├─ dashboard
│     │  └─ index.blade.php
│     ├─ dashboard.blade.php
│     ├─ layouts
│     │  ├─ app.blade.php
│     │  ├─ base.blade.php
│     │  ├─ business-app.blade.php
│     │  ├─ business-nav.blade.php
│     │  ├─ business-sidenav.blade.php
│     │  ├─ business-topbar.blade.php
│     │  ├─ footer.blade.php
│     │  ├─ footer2.blade.php
│     │  ├─ nav.blade.php
│     │  ├─ sidenav.blade.php
│     │  ├─ superadmin-app.blade.php
│     │  ├─ superadmin-nav.blade.php
│     │  ├─ superadmin-sidenav.blade.php
│     │  ├─ superadmin-topbar.blade.php
│     │  └─ topbar.blade.php
│     ├─ livewire
│     │  └─ auth
│     │     ├─ login.blade.php
│     │     ├─ register-wizard.blade.php
│     │     └─ register.blade.php
│     ├─ lock.blade.php
│     ├─ mobile-config.blade.php
│     ├─ orders
│     │  ├─ associate-error.blade.php
│     │  ├─ associate-success.blade.php
│     │  ├─ create.blade.php
│     │  ├─ edit.blade.php
│     │  ├─ index.blade.php
│     │  └─ show.blade.php
│     ├─ partials
│     │  └─ footer.blade.php
│     ├─ payments
│     │  ├─ cancel.blade.php
│     │  ├─ checkout.blade.php
│     │  ├─ history.blade.php
│     │  ├─ index.blade.php
│     │  └─ success.blade.php
│     ├─ reset-password-example.blade.php
│     ├─ superadmin
│     │  ├─ auth
│     │  │  └─ login.blade.php
│     │  ├─ businesses
│     │  │  ├─ edit.blade.php
│     │  │  ├─ index.blade.php
│     │  │  └─ show.blade.php
│     │  ├─ dashboard.blade.php
│     │  ├─ login.blade.php
│     │  ├─ orders
│     │  │  └─ index.blade.php
│     │  ├─ payments
│     │  │  └─ index.blade.php
│     │  ├─ plans
│     │  │  ├─ create.blade.php
│     │  │  ├─ edit.blade.php
│     │  │  ├─ index.blade.php
│     │  │  └─ show.blade.php
│     │  ├─ profile
│     │  │  ├─ edit.blade.php
│     │  │  └─ index.blade.php
│     │  ├─ reports
│     │  │  └─ index.blade.php
│     │  └─ tickets
│     │     ├─ index.blade.php
│     │     ├─ respond.blade.php
│     │     └─ show.blade.php
│     ├─ support
│     │  ├─ create.blade.php
│     │  ├─ edit.blade.php
│     │  ├─ index.blade.php
│     │  └─ show.blade.php
│     ├─ test-icons.blade.php
│     ├─ test-scanner.blade.php
│     └─ transactions.blade.php
├─ routes
│  ├─ api.php
│  ├─ channels.php
│  ├─ console.php
│  └─ web.php
├─ RULES_BLADE.md
├─ server.php
├─ storage
│  ├─ app
│  │  └─ public
│  │     ├─ businesses
│  │     │  ├─ C1uJDPM5hFiYLFYdxqRBvPQ9WeHpS79sQQXARapX.png
│  │     │  └─ lBbX22hB5ZgPNE57O4TZe8AFu8oRIaQQBWl0YmGs.png
│  │     ├─ qr_codes
│  │     │  ├─ 1
│  │     │  │  └─ order_1_PKn0EX6fdYExQVgDzEUaKuGnGRTTlfIo.svg
│  │     │  ├─ 10
│  │     │  │  ├─ order_18_ela17oHY5bFzkd48V8PlfQwIDe6j6rBo.svg
│  │     │  │  ├─ order_19_RxRNdSKMtSm3ptXhAb2pdtuSVNFY0cTD.svg
│  │     │  │  └─ order_20_8S5qVy4vD6JSke6Md0u8BiAfZtcdiTsg.svg
│  │     │  ├─ 4
│  │     │  │  ├─ order_10_njubYgkZN7mkx2wUnIGNk5sqDXdA28eN.svg
│  │     │  │  ├─ order_11_zU79wy4kkCWzgOS8LiPL2EkLvn6mHpqw.svg
│  │     │  │  ├─ order_12_bbvuuj2ZSjTkyU14fRSdmuHxqUc5ZPca.svg
│  │     │  │  ├─ order_13_1aYiwlC7LLSRm3YsnFfRGioPnSp0Q00Y.svg
│  │     │  │  ├─ order_14_xtx3UCNjOuPLemJEnsjFQOotHbEsiUBP.svg
│  │     │  │  ├─ order_15_KFwHP6OTkCexxOWM8beOgrT1sge985F5.svg
│  │     │  │  ├─ order_16_BExGDaeox1puTQICTz8KpMHDlIBVn6ys.svg
│  │     │  │  ├─ order_17_TnIU5m9VCoXZBpIC5BSEIY5ZVOe6gZYT.svg
│  │     │  │  ├─ order_21_gyMbWy5BuFFFqFdTjwiaHkma3ZUHQt7A.svg
│  │     │  │  ├─ order_22_WTg9LydBgOAk4KEGg0jzJ8MNAdrxfK6C.svg
│  │     │  │  ├─ order_23_wf0Hy5Ml5svddObeqteSlMIsYZv1OS1h.svg
│  │     │  │  ├─ order_24_oYwZCip6bEAT6epBOoYgcHpODQO5ohvI.svg
│  │     │  │  ├─ order_25_tzM5QBL15h8KgQSdaJ7LiwU4xJRQ0H9U.svg
│  │     │  │  ├─ order_26_tA3dAkp1vtQvxAZsy5ZF4xZgP0WHIH1g.svg
│  │     │  │  ├─ order_7_46NJs1vHNfDguC3A7mgjygnpipE30Jxy.svg
│  │     │  │  ├─ order_8_cCSeMJt7AzyNvXZBDLYpbMB0W19XK0l4.svg
│  │     │  │  └─ order_9_D7KO6UWd3F5J21DBgiHG9dBceUGJBPsY.svg
│  │     │  ├─ 6
│  │     │  │  └─ order_3_JiUwqI2dt8XKHnl1u253ObllgAB7UvHF.svg
│  │     │  └─ 9
│  │     │     ├─ order_3_1HcsQq0zEHBLwQ8YuZ0lO1gyE1JwGEd8.svg
│  │     │     ├─ order_4_1rh1HqB3duv2H3hFB9AiUZwvjg8PcJjE.svg
│  │     │     ├─ order_5_C2Xsr0iqQWl2Y1r3zYvl6wPQaS8syuIL.svg
│  │     │     ├─ order_6_WB9dWfW83gbzhn7hznPbwPQcUvcopayB.svg
│  │     │     ├─ order_7_5m1fKOLEdLArpFVkHhxOc96Ndo8xoOJW.svg
│  │     │     ├─ order_8_sPNm6azJO0VX8dnxaIBeyUDPwONdHQl5.svg
│  │     │     └─ order_9_tmnJRFLWsU2ZyjSUimdxzYTIZ9WXATlx.svg
│  │     └─ support_tickets
│  │        └─ 1764891254_RP3GRUPO4.pdf
│  ├─ framework
│  │  ├─ cache
│  │  │  └─ data
│  │  ├─ sessions
│  │  │  └─ FnZWZJ92747hG8at9V2EyfkUvK4fFBmiDfwvvZ2L
│  │  ├─ testing
│  │  └─ views
│  │     ├─ 012f02babe8043b3f5dd5466a1ea55df.php
│  │     ├─ 02c741497a9d371c6644c22aab6992a9.php
│  │     ├─ 05c3e7257f7c94c9e689c445847f8d54.php
│  │     ├─ 07961c45b506dd628b7a5aaff0d4fa4b.php
│  │     ├─ 0b3526ec9d070716bcd9513677da3b79.php
│  │     ├─ 100bbb02a3874cea88d647a43f30d7eb.php
│  │     ├─ 1bcd7fc1ac35af26612e6d079c3ba0b5.php
│  │     ├─ 2b38b68370bb94ddcb2fb2d4f87e4b05.php
│  │     ├─ 2ee78e093e9529ef7ac9e3661ce4e758.php
│  │     ├─ 38fce379140ab096125ac06052154295.php
│  │     ├─ 3e6d29f0434ce98d12903eb1a3314392.php
│  │     ├─ 3f6590c1853a90298254302473725e38.php
│  │     ├─ 4943bc92ebba41e8b0e508149542e0ad.blade.php
│  │     ├─ 4b14eb9880225e925e000bcf8066e087.php
│  │     ├─ 4bac13ee6720971970b4e755312506d6.php
│  │     ├─ 513f20e7dbc6c67463531064228ef2ba.php
│  │     ├─ 514f50832eef764dc72cd4382063e91d.php
│  │     ├─ 59a64c88a20dbb7d7aa1e200ad3d9617.php
│  │     ├─ 697d3ca3e662ff3593a16723cf559a50.php
│  │     ├─ 6fa3b4d99a1388bd8fa05d7b02dbb9b3.php
│  │     ├─ 78983f437860e88b691d6e619ff5b972.php
│  │     ├─ 7e80cef5434d059cee5123d3dd6da1cb.php
│  │     ├─ 867e4fa9f108c8603d22db9e6551ca87.php
│  │     ├─ 870dd7e12b55c0e450a8882ddde457da.php
│  │     ├─ 89fd5a228428bf7362846c13fbb75827.php
│  │     ├─ 8f6cc112dcb00121ce7d53c9d53ebd68.php
│  │     ├─ 8ff8a2340ce1cfbe51d8e76435d806e3.php
│  │     ├─ 9c4994bf635bf8f79bb2cb8bdc936720.php
│  │     ├─ 9f873283ce20e7d326a3060a8c057c67.php
│  │     ├─ a0f7ea936e1cad44a87dc47ac3cf26c0.php
│  │     ├─ a65d7795b59d7b4d6f36d9f9f5e96f4a.php
│  │     ├─ a6a6273431e608dedd2cd07047f08862.php
│  │     ├─ aaf5965f42b85ad089bd7acf3242017b.php
│  │     ├─ bee77e39d268564844b229bebdd1bdfd.php
│  │     ├─ c0a29f0d3915e3b80e5a62c9162c8158.php
│  │     ├─ c2ca54fd352d647d19aed46ebeafee91.php
│  │     ├─ cb2aaa4e14572cb74e7fff851a611235.php
│  │     ├─ cf116eb1e19f9c073339c239f4fca283.php
│  │     ├─ d535dba59333b412418247aa30b6ffcc.php
│  │     ├─ d81d82c57923cd3e8d5df5b409076e26.php
│  │     ├─ d958d8b14b4cbfbccf211c93a3f6a450.php
│  │     ├─ e515f540ba6ec3139ceb76da9c331442.php
│  │     ├─ eb5d9734d7adff657378a6ec0c26cb93.php
│  │     ├─ ec5067c0899b252fad09cba2d6f8bf39.php
│  │     ├─ f0f1b03685ce2d9ad66751c62ebaf506.php
│  │     ├─ f22060def345103e63860e6086b9aec1.php
│  │     └─ f46943f3f71be27739c435092996f3bb.php
│  └─ logs
├─ tailwind.config.js
├─ tests
│  ├─ CreatesApplication.php
│  ├─ Feature
│  │  └─ ExampleTest.php
│  ├─ TestCase.php
│  └─ Unit
│     └─ ExampleTest.php
└─ webpack.mix.js

```
```
Focus-laravel-QR
├─ .claude-instructions
├─ .editorconfig
├─ .nvmrc
├─ .phpunit.cache
│  └─ test-results
├─ .railwayignore
├─ .styleci.yml
├─ agregar_ubicaciones_prueba.php
├─ app
│  ├─ Console
│  │  ├─ Commands
│  │  │  ├─ CheckExpiredPayments.php
│  │  │  ├─ CleanExpiredOrders.php
│  │  │  ├─ GenerateSystemReport.php
│  │  │  ├─ SendOrderRealerts.php
│  │  │  ├─ SendPaymentReminders.php
│  │  │  └─ TestMercadoPago.php
│  │  └─ Kernel.php
│  ├─ Exceptions
│  │  └─ Handler.php
│  ├─ Http
│  │  ├─ Controllers
│  │  │  ├─ Api
│  │  │  │  ├─ BusinessApiController.php
│  │  │  │  ├─ ChatApiController.php
│  │  │  │  ├─ MobileAuthController.php
│  │  │  │  └─ V1
│  │  │  │     ├─ Auth
│  │  │  │     │  └─ AuthController.php
│  │  │  │     ├─ BusinessLocationController.php
│  │  │  │     └─ MobileController.php
│  │  │  ├─ Auth
│  │  │  │  └─ AuthController.php
│  │  │  ├─ BusinessController.php
│  │  │  ├─ ChatController.php
│  │  │  ├─ Controller.php
│  │  │  ├─ CS
│  │  │  │  ├─ DashboardController.php
│  │  │  │  └─ OrderController.php
│  │  │  ├─ DashboardController.php
│  │  │  ├─ MercadoPagoWebhookController.php
│  │  │  ├─ OrderController.php
│  │  │  ├─ PaymentController.php
│  │  │  ├─ SuperAdmin
│  │  │  │  ├─ AuthController.php
│  │  │  │  ├─ BusinessManagementController.php
│  │  │  │  ├─ DashboardController.php
│  │  │  │  ├─ GlobalOrderController.php
│  │  │  │  ├─ PaymentManagementController.php
│  │  │  │  ├─ PlanManagementController.php
│  │  │  │  ├─ ProfileController.php
│  │  │  │  ├─ ReportController.php
│  │  │  │  └─ TicketManagementController.php
│  │  │  └─ SupportTicketController.php
│  │  ├─ Kernel.php
│  │  ├─ Middleware
│  │  │  ├─ Authenticate.php
│  │  │  ├─ CheckActiveSubscription.php
│  │  │  ├─ CheckBusinessPayment.php
│  │  │  ├─ EncryptCookies.php
│  │  │  ├─ MobileDeviceMiddleware.php
│  │  │  ├─ OptionalSanctumAuth.php
│  │  │  ├─ PreventRequestsDuringMaintenance.php
│  │  │  ├─ RedirectIfAuthenticated.php
│  │  │  ├─ TrimStrings.php
│  │  │  ├─ TrustHosts.php
│  │  │  ├─ TrustProxies.php
│  │  │  └─ VerifyCsrfToken.php
│  │  └─ Requests
│  │     ├─ CreateBusinessRequest.php
│  │     ├─ CreateOrderRequest.php
│  │     ├─ CreatePaymentRequest.php
│  │     ├─ CreateSupportTicketRequest.php
│  │     ├─ NearbyBusinessesRequest.php
│  │     ├─ SearchBusinessesRequest.php
│  │     ├─ UpdateBusinessRequest.php
│  │     └─ UpdateOrderRequest.php
│  ├─ Livewire
│  │  └─ Auth
│  │     ├─ Login.php
│  │     ├─ Register.php
│  │     └─ RegisterWizard.php
│  ├─ Models
│  │  ├─ Business.php
│  │  ├─ ChatMessage.php
│  │  ├─ MobileDevice.php
│  │  ├─ MobileUser.php
│  │  ├─ Notification.php
│  │  ├─ Order.php
│  │  ├─ OrderItem.php
│  │  ├─ OrderRealert.php
│  │  ├─ OrderStatusHistory.php
│  │  ├─ Payment.php
│  │  ├─ Plan.php
│  │  ├─ SuperAdmin.php
│  │  ├─ SupportTicket.php
│  │  └─ User.php
│  ├─ Notifications
│  │  └─ ResetPassword.php
│  ├─ Policies
│  │  ├─ OrderPolicy.php
│  │  └─ SupportTicketPolicy.php
│  ├─ Providers
│  │  ├─ AppServiceProvider.php
│  │  ├─ AuthServiceProvider.php
│  │  ├─ BroadcastServiceProvider.php
│  │  ├─ EventServiceProvider.php
│  │  └─ RouteServiceProvider.php
│  ├─ Services
│  │  ├─ MercadoPagoService.php
│  │  ├─ NotificationService.php
│  │  ├─ OrderService.php
│  │  ├─ PaymentService.php
│  │  ├─ PushNotificationService.php
│  │  └─ QrCodeService.php
│  └─ View
│     └─ Components
│        ├─ CS
│        │  └─ Alert.php
│        ├─ Icon.php
│        └─ Layouts
│           └─ Base.php
├─ artisan
├─ bootstrap
│  ├─ app.php
│  └─ cache
│     ├─ packages.php
│     └─ services.php
├─ composer.json
├─ composer.lock
├─ config
│  ├─ app.php
│  ├─ auth.php
│  ├─ broadcasting.php
│  ├─ cache.php
│  ├─ cetam.cs.php
│  ├─ cors.php
│  ├─ database.php
│  ├─ filesystems.php
│  ├─ hashing.php
│  ├─ icons.php
│  ├─ livewire.php
│  ├─ logging.php
│  ├─ mail.php
│  ├─ proj.php
│  ├─ queue.php
│  ├─ sanctum.php
│  ├─ services.php
│  ├─ session.php
│  └─ view.php
├─ controllers-inventory.txt
├─ database
│  ├─ factories
│  │  └─ UserFactory.php
│  ├─ migrations
│  │  ├─ 2014_10_12_000000_create_users_table.php
│  │  ├─ 2014_10_12_100000_create_password_resets_table.php
│  │  ├─ 2019_08_19_000000_create_failed_jobs_table.php
│  │  ├─ 2019_12_14_000001_create_personal_access_tokens_table.php
│  │  ├─ 2025_11_03_204427_create_plans_table.php
│  │  ├─ 2025_11_03_204454_create_super_admins_table.php
│  │  ├─ 2025_11_03_204519_create_businesses_table.php
│  │  ├─ 2025_11_03_204548_create_orders_table.php
│  │  ├─ 2025_11_03_204618_create_mobile_devices_table.php
│  │  ├─ 2025_11_03_204645_create_notifications_table.php
│  │  ├─ 2025_11_03_204715_create_payments_table.php
│  │  ├─ 2025_11_03_204745_create_support_tickets_table.php
│  │  ├─ 2025_11_04_155034_add_modular_pricing_to_plans_and_businesses.php
│  │  ├─ 2025_11_06_091909_create_mobile_users_table.php
│  │  ├─ 2025_11_06_091917_create_order_items_table.php
│  │  ├─ 2025_11_06_091917_create_order_status_history_table.php
│  │  ├─ 2025_11_06_092001_add_mobile_fields_to_orders_table.php
│  │  ├─ 2025_11_14_151759_add_location_and_photo_to_businesses_table.php
│  │  ├─ 2025_11_14_153530_add_soft_deletes_to_orders_table.php
│  │  ├─ 2025_11_19_082110_add_chat_and_realerts_to_plans_table.php
│  │  ├─ 2025_11_19_082426_create_order_realerts_table.php
│  │  ├─ 2025_11_19_083936_add_realert_time_fields_to_plans_table.php
│  │  ├─ 2025_11_19_090028_add_photo_to_businesses_table.php
│  │  ├─ 2025_11_19_091635_remove_unused_fields_from_businesses_table.php
│  │  ├─ 2025_11_19_134413_add_attachment_url_to_support_tickets_table.php
│  │  ├─ 2025_11_19_142205_add_response_attachment_url_to_support_tickets_table.php
│  │  ├─ 2025_11_19_180843_create_chat_messages_table.php
│  │  ├─ 2025_11_19_230256_add_google_fields_to_users_table.php
│  │  ├─ 2025_11_19_230744_add_name_column_to_users_table.php
│  │  ├─ 2025_11_19_232557_add_user_id_to_orders_table.php
│  │  ├─ 2025_11_20_100642_add_mercadopago_fields_to_payments_and_businesses_table.php
│  │  ├─ 2025_11_26_144109_add_location_fields_to_businesses_table.php
│  │  ├─ 2025_11_27_140452_add_auth_fields_to_mobile_users_table.php
│  │  ├─ 2025_12_09_154621_add_business_folio_to_orders_table.php
│  │  ├─ 2025_12_09_160835_remove_unique_constraint_from_business_folio.php
│  │  ├─ 2025_12_10_143058_remove_location_description_from_businesses_table.php
│  │  └─ 2025_12_17_003325_add_logo_url_to_businesses_table.php
│  └─ seeders
│     ├─ BusinessSeeder.php
│     ├─ DatabaseSeeder.php
│     ├─ DemoOrdersSeeder.php
│     ├─ MobileAppSeeder.php
│     ├─ OrderSeeder.php
│     ├─ PaymentSeeder.php
│     ├─ PlanSeeder.php
│     ├─ SuperAdminSeeder.php
│     ├─ SupportTicketSeeder.php
│     └─ UserSeeder.php
├─ estructura.txt
├─ models-inventory.txt
├─ nixpacks.toml
├─ package-lock.json
├─ package.json
├─ phpunit.xml
├─ Procfile
├─ project-analysis.txt
├─ public
│  ├─ .htaccess
│  ├─ assets
│  │  ├─ img
│  │  │  ├─ brand
│  │  │  │  ├─ dark.svg
│  │  │  │  └─ light.svg
│  │  │  ├─ favicon
│  │  │  │  ├─ android-chrome-192x192.png
│  │  │  │  ├─ android-chrome-512x512.png
│  │  │  │  ├─ apple-touch-icon.png
│  │  │  │  ├─ browserconfig.xml
│  │  │  │  ├─ favicon-16x16.png
│  │  │  │  ├─ favicon-32x32.png
│  │  │  │  ├─ favicon.ico
│  │  │  │  ├─ manifest.json
│  │  │  │  ├─ mstile-150x150.png
│  │  │  │  ├─ safari-pinned-tab.svg
│  │  │  │  └─ site.webmanifest
│  │  │  ├─ focus-icon.svg
│  │  │  ├─ icons
│  │  │  │  ├─ github.svg
│  │  │  │  ├─ google-tag-manager.svg
│  │  │  │  └─ google_analytics.svg
│  │  │  ├─ illustrations
│  │  │  │  ├─ 404.svg
│  │  │  │  ├─ 500.svg
│  │  │  │  └─ signin.svg
│  │  │  ├─ marker.svg
│  │  │  ├─ paypal-logo.svg
│  │  │  ├─ technologies
│  │  │  │  ├─ bootstrap-5-logo.svg
│  │  │  │  ├─ react-logo-primary.svg
│  │  │  │  ├─ react-logo.svg
│  │  │  │  └─ vuejs-logo.svg
│  │  │  ├─ themesberg-logo-alt.svg
│  │  │  └─ themesberg.svg
│  │  └─ js
│  │     ├─ bootstrap.min.js
│  │     ├─ chartist-plugin-tooltip.min.js
│  │     ├─ chartist.min.js
│  │     ├─ datepicker.min.js
│  │     ├─ demo.js
│  │     ├─ nouislider.min.js
│  │     ├─ on-screen.umd.min.js
│  │     ├─ popper.min.js
│  │     ├─ simplebar.min.js
│  │     ├─ smooth-scroll.polyfills.min.js
│  │     ├─ sweetalert2.all.min.js
│  │     └─ volt.js
│  ├─ css
│  │  ├─ all.min.css
│  │  ├─ app.css
│  │  ├─ cetam-colors.css
│  │  ├─ cetam-sidebar.css
│  │  └─ volt.css
│  ├─ favicon.ico
│  ├─ index.php
│  ├─ js
│  │  ├─ app.js
│  │  └─ app.js.LICENSE.txt
│  ├─ mix-manifest.json
│  ├─ robots.txt
│  └─ web.config
├─ README.md
├─ resources
│  ├─ css
│  │  └─ app.css
│  ├─ js
│  │  ├─ app.js
│  │  ├─ bootstrap.js
│  │  └─ notifications.js
│  ├─ lang
│  │  └─ en
│  │     ├─ auth.php
│  │     ├─ pagination.php
│  │     ├─ passwords.php
│  │     └─ validation.php
│  ├─ sass
│  │  ├─ custom
│  │  │  ├─ _components.scss
│  │  │  ├─ _custom.scss
│  │  │  └─ _variables.scss
│  │  ├─ volt
│  │  │  ├─ components
│  │  │  │  ├─ _accordions.scss
│  │  │  │  ├─ _alerts.scss
│  │  │  │  ├─ _animations.scss
│  │  │  │  ├─ _avatars.scss
│  │  │  │  ├─ _badge.scss
│  │  │  │  ├─ _body.scss
│  │  │  │  ├─ _breadcrumb.scss
│  │  │  │  ├─ _buttons.scss
│  │  │  │  ├─ _card.scss
│  │  │  │  ├─ _carousel.scss
│  │  │  │  ├─ _charts.scss
│  │  │  │  ├─ _close.scss
│  │  │  │  ├─ _custom-forms.scss
│  │  │  │  ├─ _datepicker.scss
│  │  │  │  ├─ _dropdown.scss
│  │  │  │  ├─ _icons.scss
│  │  │  │  ├─ _images.scss
│  │  │  │  ├─ _list-group.scss
│  │  │  │  ├─ _modal.scss
│  │  │  │  ├─ _nav.scss
│  │  │  │  ├─ _pagination.scss
│  │  │  │  ├─ _popover.scss
│  │  │  │  ├─ _progress.scss
│  │  │  │  ├─ _scrollbar.scss
│  │  │  │  ├─ _shapes.scss
│  │  │  │  ├─ _steps.scss
│  │  │  │  ├─ _tables.scss
│  │  │  │  ├─ _timelines.scss
│  │  │  │  ├─ _tooltip.scss
│  │  │  │  └─ _type.scss
│  │  │  ├─ forms
│  │  │  │  ├─ _form-check.scss
│  │  │  │  ├─ _form-control.scss
│  │  │  │  ├─ _form-select.scss
│  │  │  │  └─ _input-group.scss
│  │  │  ├─ layout
│  │  │  │  ├─ _footer.scss
│  │  │  │  ├─ _navbar.scss
│  │  │  │  ├─ _section.scss
│  │  │  │  ├─ _sidebar.scss
│  │  │  │  └─ _sidenav.scss
│  │  │  ├─ mixins
│  │  │  │  ├─ _animations.scss
│  │  │  │  ├─ _background-variant.scss
│  │  │  │  ├─ _icon.scss
│  │  │  │  ├─ _modals.scss
│  │  │  │  ├─ _popover.scss
│  │  │  │  ├─ _transform.scss
│  │  │  │  └─ _utilities.scss
│  │  │  ├─ _components.scss
│  │  │  ├─ _forms.scss
│  │  │  ├─ _functions.scss
│  │  │  ├─ _layout.scss
│  │  │  ├─ _mixins.scss
│  │  │  ├─ _utilities.scss
│  │  │  ├─ _variables.scss
│  │  │  └─ _vendor.scss
│  │  └─ volt.scss
│  └─ views
│     ├─ 404.blade.php
│     ├─ 500.blade.php
│     ├─ auth
│     │  └─ login.blade.php
│     ├─ bootstrap-tables.blade.php
│     ├─ business
│     │  ├─ change-password.blade.php
│     │  ├─ edit.blade.php
│     │  ├─ profile.blade.php
│     │  └─ register.blade.php
│     ├─ chat
│     │  └─ index.blade.php
│     ├─ components
│     │  ├─ alert.blade.php
│     │  ├─ button.blade.php
│     │  ├─ buttons.blade.php
│     │  ├─ chat-widget.blade.php
│     │  ├─ cs
│     │  │  └─ alert.blade.php
│     │  ├─ forms.blade.php
│     │  ├─ icon.blade.php
│     │  ├─ legal-content
│     │  │  ├─ privacy.blade.php
│     │  │  └─ terms.blade.php
│     │  ├─ legal-modals.blade.php
│     │  ├─ modal.blade.php
│     │  ├─ modals.blade.php
│     │  ├─ notifications.blade.php
│     │  └─ typography.blade.php
│     ├─ dashboard
│     │  └─ index.blade.php
│     ├─ dashboard.blade.php
│     ├─ layouts
│     │  ├─ app.blade.php
│     │  ├─ base.blade.php
│     │  ├─ business-app.blade.php
│     │  ├─ business-nav.blade.php
│     │  ├─ business-sidenav.blade.php
│     │  ├─ business-topbar.blade.php
│     │  ├─ footer.blade.php
│     │  ├─ footer2.blade.php
│     │  ├─ nav.blade.php
│     │  ├─ sidenav.blade.php
│     │  ├─ superadmin-app.blade.php
│     │  ├─ superadmin-nav.blade.php
│     │  ├─ superadmin-sidenav.blade.php
│     │  ├─ superadmin-topbar.blade.php
│     │  └─ topbar.blade.php
│     ├─ livewire
│     │  └─ auth
│     │     ├─ login.blade.php
│     │     ├─ register-wizard.blade.php
│     │     └─ register.blade.php
│     ├─ lock.blade.php
│     ├─ mobile-config.blade.php
│     ├─ orders
│     │  ├─ associate-error.blade.php
│     │  ├─ associate-success.blade.php
│     │  ├─ create.blade.php
│     │  ├─ edit.blade.php
│     │  ├─ index.blade.php
│     │  └─ show.blade.php
│     ├─ partials
│     │  └─ footer.blade.php
│     ├─ payments
│     │  ├─ cancel.blade.php
│     │  ├─ checkout.blade.php
│     │  ├─ history.blade.php
│     │  ├─ index.blade.php
│     │  └─ success.blade.php
│     ├─ reset-password-example.blade.php
│     ├─ superadmin
│     │  ├─ auth
│     │  │  └─ login.blade.php
│     │  ├─ businesses
│     │  │  ├─ edit.blade.php
│     │  │  ├─ index.blade.php
│     │  │  └─ show.blade.php
│     │  ├─ dashboard.blade.php
│     │  ├─ login.blade.php
│     │  ├─ orders
│     │  │  └─ index.blade.php
│     │  ├─ payments
│     │  │  └─ index.blade.php
│     │  ├─ plans
│     │  │  ├─ create.blade.php
│     │  │  ├─ edit.blade.php
│     │  │  ├─ index.blade.php
│     │  │  └─ show.blade.php
│     │  ├─ profile
│     │  │  ├─ edit.blade.php
│     │  │  └─ index.blade.php
│     │  ├─ reports
│     │  │  └─ index.blade.php
│     │  └─ tickets
│     │     ├─ index.blade.php
│     │     ├─ respond.blade.php
│     │     └─ show.blade.php
│     ├─ support
│     │  ├─ create.blade.php
│     │  ├─ edit.blade.php
│     │  ├─ index.blade.php
│     │  └─ show.blade.php
│     ├─ test-icons.blade.php
│     ├─ test-scanner.blade.php
│     └─ transactions.blade.php
├─ routes
│  ├─ api.php
│  ├─ channels.php
│  ├─ console.php
│  └─ web.php
├─ RULES_BLADE.md
├─ server.php
├─ storage
│  ├─ app
│  │  └─ public
│  │     ├─ businesses
│  │     │  ├─ C1uJDPM5hFiYLFYdxqRBvPQ9WeHpS79sQQXARapX.png
│  │     │  └─ lBbX22hB5ZgPNE57O4TZe8AFu8oRIaQQBWl0YmGs.png
│  │     ├─ qr_codes
│  │     │  ├─ 1
│  │     │  │  └─ order_1_PKn0EX6fdYExQVgDzEUaKuGnGRTTlfIo.svg
│  │     │  ├─ 10
│  │     │  │  ├─ order_18_ela17oHY5bFzkd48V8PlfQwIDe6j6rBo.svg
│  │     │  │  ├─ order_19_RxRNdSKMtSm3ptXhAb2pdtuSVNFY0cTD.svg
│  │     │  │  └─ order_20_8S5qVy4vD6JSke6Md0u8BiAfZtcdiTsg.svg
│  │     │  ├─ 4
│  │     │  │  ├─ order_10_njubYgkZN7mkx2wUnIGNk5sqDXdA28eN.svg
│  │     │  │  ├─ order_11_zU79wy4kkCWzgOS8LiPL2EkLvn6mHpqw.svg
│  │     │  │  ├─ order_12_bbvuuj2ZSjTkyU14fRSdmuHxqUc5ZPca.svg
│  │     │  │  ├─ order_13_1aYiwlC7LLSRm3YsnFfRGioPnSp0Q00Y.svg
│  │     │  │  ├─ order_14_xtx3UCNjOuPLemJEnsjFQOotHbEsiUBP.svg
│  │     │  │  ├─ order_15_KFwHP6OTkCexxOWM8beOgrT1sge985F5.svg
│  │     │  │  ├─ order_16_BExGDaeox1puTQICTz8KpMHDlIBVn6ys.svg
│  │     │  │  ├─ order_17_TnIU5m9VCoXZBpIC5BSEIY5ZVOe6gZYT.svg
│  │     │  │  ├─ order_21_gyMbWy5BuFFFqFdTjwiaHkma3ZUHQt7A.svg
│  │     │  │  ├─ order_22_WTg9LydBgOAk4KEGg0jzJ8MNAdrxfK6C.svg
│  │     │  │  ├─ order_23_wf0Hy5Ml5svddObeqteSlMIsYZv1OS1h.svg
│  │     │  │  ├─ order_24_oYwZCip6bEAT6epBOoYgcHpODQO5ohvI.svg
│  │     │  │  ├─ order_25_tzM5QBL15h8KgQSdaJ7LiwU4xJRQ0H9U.svg
│  │     │  │  ├─ order_26_tA3dAkp1vtQvxAZsy5ZF4xZgP0WHIH1g.svg
│  │     │  │  ├─ order_7_46NJs1vHNfDguC3A7mgjygnpipE30Jxy.svg
│  │     │  │  ├─ order_8_cCSeMJt7AzyNvXZBDLYpbMB0W19XK0l4.svg
│  │     │  │  └─ order_9_D7KO6UWd3F5J21DBgiHG9dBceUGJBPsY.svg
│  │     │  ├─ 6
│  │     │  │  └─ order_3_JiUwqI2dt8XKHnl1u253ObllgAB7UvHF.svg
│  │     │  └─ 9
│  │     │     ├─ order_3_1HcsQq0zEHBLwQ8YuZ0lO1gyE1JwGEd8.svg
│  │     │     ├─ order_4_1rh1HqB3duv2H3hFB9AiUZwvjg8PcJjE.svg
│  │     │     ├─ order_5_C2Xsr0iqQWl2Y1r3zYvl6wPQaS8syuIL.svg
│  │     │     ├─ order_6_WB9dWfW83gbzhn7hznPbwPQcUvcopayB.svg
│  │     │     ├─ order_7_5m1fKOLEdLArpFVkHhxOc96Ndo8xoOJW.svg
│  │     │     ├─ order_8_sPNm6azJO0VX8dnxaIBeyUDPwONdHQl5.svg
│  │     │     └─ order_9_tmnJRFLWsU2ZyjSUimdxzYTIZ9WXATlx.svg
│  │     └─ support_tickets
│  │        └─ 1764891254_RP3GRUPO4.pdf
│  ├─ framework
│  │  ├─ cache
│  │  │  └─ data
│  │  ├─ sessions
│  │  │  └─ FnZWZJ92747hG8at9V2EyfkUvK4fFBmiDfwvvZ2L
│  │  ├─ testing
│  │  └─ views
│  │     ├─ 012f02babe8043b3f5dd5466a1ea55df.php
│  │     ├─ 02c741497a9d371c6644c22aab6992a9.php
│  │     ├─ 05c3e7257f7c94c9e689c445847f8d54.php
│  │     ├─ 07961c45b506dd628b7a5aaff0d4fa4b.php
│  │     ├─ 0b3526ec9d070716bcd9513677da3b79.php
│  │     ├─ 100bbb02a3874cea88d647a43f30d7eb.php
│  │     ├─ 1bcd7fc1ac35af26612e6d079c3ba0b5.php
│  │     ├─ 2b38b68370bb94ddcb2fb2d4f87e4b05.php
│  │     ├─ 2ee78e093e9529ef7ac9e3661ce4e758.php
│  │     ├─ 38fce379140ab096125ac06052154295.php
│  │     ├─ 3e6d29f0434ce98d12903eb1a3314392.php
│  │     ├─ 3f6590c1853a90298254302473725e38.php
│  │     ├─ 4943bc92ebba41e8b0e508149542e0ad.blade.php
│  │     ├─ 4b14eb9880225e925e000bcf8066e087.php
│  │     ├─ 4bac13ee6720971970b4e755312506d6.php
│  │     ├─ 513f20e7dbc6c67463531064228ef2ba.php
│  │     ├─ 514f50832eef764dc72cd4382063e91d.php
│  │     ├─ 59a64c88a20dbb7d7aa1e200ad3d9617.php
│  │     ├─ 697d3ca3e662ff3593a16723cf559a50.php
│  │     ├─ 6fa3b4d99a1388bd8fa05d7b02dbb9b3.php
│  │     ├─ 78983f437860e88b691d6e619ff5b972.php
│  │     ├─ 7e80cef5434d059cee5123d3dd6da1cb.php
│  │     ├─ 867e4fa9f108c8603d22db9e6551ca87.php
│  │     ├─ 870dd7e12b55c0e450a8882ddde457da.php
│  │     ├─ 89fd5a228428bf7362846c13fbb75827.php
│  │     ├─ 8f6cc112dcb00121ce7d53c9d53ebd68.php
│  │     ├─ 8ff8a2340ce1cfbe51d8e76435d806e3.php
│  │     ├─ 9c4994bf635bf8f79bb2cb8bdc936720.php
│  │     ├─ 9f873283ce20e7d326a3060a8c057c67.php
│  │     ├─ a0f7ea936e1cad44a87dc47ac3cf26c0.php
│  │     ├─ a65d7795b59d7b4d6f36d9f9f5e96f4a.php
│  │     ├─ a6a6273431e608dedd2cd07047f08862.php
│  │     ├─ aaf5965f42b85ad089bd7acf3242017b.php
│  │     ├─ bee77e39d268564844b229bebdd1bdfd.php
│  │     ├─ c0a29f0d3915e3b80e5a62c9162c8158.php
│  │     ├─ c2ca54fd352d647d19aed46ebeafee91.php
│  │     ├─ cb2aaa4e14572cb74e7fff851a611235.php
│  │     ├─ cf116eb1e19f9c073339c239f4fca283.php
│  │     ├─ d535dba59333b412418247aa30b6ffcc.php
│  │     ├─ d81d82c57923cd3e8d5df5b409076e26.php
│  │     ├─ d958d8b14b4cbfbccf211c93a3f6a450.php
│  │     ├─ e515f540ba6ec3139ceb76da9c331442.php
│  │     ├─ eb5d9734d7adff657378a6ec0c26cb93.php
│  │     ├─ ec5067c0899b252fad09cba2d6f8bf39.php
│  │     ├─ f0f1b03685ce2d9ad66751c62ebaf506.php
│  │     ├─ f22060def345103e63860e6086b9aec1.php
│  │     └─ f46943f3f71be27739c435092996f3bb.php
│  └─ logs
├─ tailwind.config.js
├─ tests
│  ├─ CreatesApplication.php
│  ├─ Feature
│  │  └─ ExampleTest.php
│  ├─ TestCase.php
│  └─ Unit
│     └─ ExampleTest.php
└─ webpack.mix.js

```