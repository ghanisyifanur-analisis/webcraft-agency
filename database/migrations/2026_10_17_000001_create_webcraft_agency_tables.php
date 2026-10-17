<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Clients
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('company_name', 150);
            $table->string('business_sector', 100);
            $table->string('whatsapp_no', 25);
            $table->text('address')->nullable();
            $table->timestamps();
        });

        // 2. Service Packages
        Schema::create('service_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->decimal('base_price', 12, 2);
            $table->json('features_list'); // JSON array of features
            $table->smallInteger('revision_quota')->default(2);
            $table->smallInteger('estimated_days')->default(7);
            $table->string('icon', 50)->default('globe-outline');
            $table->string('badge', 50)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        // 3. Orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique(); // ORD-YYYYMMDD-XXXX
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('package_id')->constrained('service_packages')->onDelete('restrict');
            $table->foreignId('assigned_pm_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_dev_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('total_amount', 12, 2);
            $table->string('status', 30)->default('WAITING_DP'); // WAITING_DP, BRIEF_PHASE, IN_PROGRESS, STAGING_REVIEW, COMPLETED, CANCELLED
            $table->date('start_date')->nullable();
            $table->date('target_completion_date')->nullable();
            $table->smallInteger('revisions_used')->default(0);
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        // 4. Order Addons
        Schema::create('order_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('addon_name', 100);
            $table->decimal('price', 12, 2);
            $table->smallInteger('additional_days')->default(0);
            $table->timestamps();
        });

        // 5. Brief Submissions
        Schema::create('brief_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('brand_name', 120);
            $table->text('tagline_description')->nullable();
            $table->string('color_palette', 100)->nullable();
            $table->text('target_audience')->nullable();
            $table->text('competitor_references')->nullable();
            $table->string('logo_file_path', 255)->nullable();
            $table->text('supporting_assets_url')->nullable();
            $table->text('feature_notes')->nullable();
            $table->string('status', 20)->default('SUBMITTED'); // SUBMITTED, NEED_REVISION, ACCEPTED
            $table->text('pm_feedback')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        // 6. Project Milestones
        Schema::create('project_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('title', 150);
            $table->smallInteger('stage_number'); // 1 to 5
            $table->text('description')->nullable();
            $table->string('staging_url', 255)->nullable();
            $table->string('status', 25)->default('LOCKED'); // LOCKED, ACTIVE, IN_REVIEW, APPROVED
            $table->timestamp('approved_at')->nullable();
            $table->string('approved_ip', 45)->nullable();
            $table->timestamps();
        });

        // 7. Revision Tickets
        Schema::create('revision_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('milestone_id')->constrained('project_milestones')->onDelete('cascade');
            $table->string('ticket_number', 50)->unique();
            $table->text('issue_description');
            $table->string('target_url', 255)->nullable();
            $table->string('attachment_url', 255)->nullable();
            $table->string('status', 20)->default('open'); // open, approved_for_work, in_progress, resolved, rejected
            $table->text('developer_response')->nullable();
            $table->boolean('is_paid_addon')->default(false);
            $table->timestamps();
        });

        // 8. Invoices (Split Billing Termin)
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique(); // INV-YYYYMM-XXXX
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('term_type', 30); // TERM_1_DP, TERM_2_SETTLEMENT, EXTRA_REVISION
            $table->decimal('amount', 12, 2);
            $table->date('due_date');
            $table->string('status', 20)->default('UNPAID'); // UNPAID, PAID, CANCELLED, EXPIRED
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        // 9. Payments (Webhook & Payment Gateway Handler)
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->string('payment_channel', 50); // VA_BCA, VA_MANDIRI, VA_BRI, QRIS, MANUAL_TRANSFER
            $table->string('reference_code', 100)->unique()->nullable();
            $table->decimal('amount_paid', 12, 2);
            $table->timestamp('paid_at');
            $table->string('status', 20)->default('success'); // success, pending, failed
            $table->json('gateway_payload')->nullable();
            $table->timestamps();
        });

        // 10. Credential Vaults (AES-256 Gated Release)
        Schema::create('credential_vaults', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('service_label', 80); // cPanel, Domain Registrar, WP-Admin, Database
            $table->string('login_url', 255);
            $table->string('username', 100);
            $table->text('encrypted_password'); // AES-256-CBC Encrypted
            $table->boolean('is_unlocked')->default(false); // Only true when Termin 2 is PAID
            $table->timestamps();
        });

        // 11. Financial Logs (COGS & Profit Calculation per Project)
        Schema::create('financial_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('expense_title', 150);
            $table->decimal('expense_amount', 12, 2);
            $table->string('category', 50); // domain, hosting, plugin, freelance, other
            $table->string('vendor_name', 100)->nullable();
            $table->date('expense_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_logs');
        Schema::dropIfExists('credential_vaults');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('revision_tickets');
        Schema::dropIfExists('project_milestones');
        Schema::dropIfExists('brief_submissions');
        Schema::dropIfExists('order_addons');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('service_packages');
        Schema::dropIfExists('clients');
    }
};
