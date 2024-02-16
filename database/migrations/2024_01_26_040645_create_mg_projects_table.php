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
            Schema::create('mg_projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_name');
            $table->string('project_description')->nullable();
            $table->unsignedBigInteger('assign_by'); // ID employee
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('jobs_id');
            $table->datetime('start_date')->default(now()->toDateTimeString());
            $table->datetime('end_date')->default(now()->toDateTimeString());
            $table->enum('project_status', ['onPending', 'workingOnit', 'Completed'])->nullable();
            $table->string('percentage')->nullable();
            $table->string('total_task_completed')->nullable();
            $table->string('total_task_created')->nullable();
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('mg_roles')->onDelete('cascade');
            $table->foreign('jobs_id')->references('id')->on('mg_jobs')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('mg_teams')->onDelete('cascade');
            $table->foreign('assign_by')->references('id')->on('mg_employee')->onDelete('cascade');
        });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('mg_projects');
        }
    };
