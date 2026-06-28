<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->integer('version_number')->default(1);
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('document_state_id')->constrained('document_states');
            $table->timestamps();
            $table->softDeletes();
        });

        // Add current_version_id to documents (nullable initially for migration)
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('current_version_id')->nullable()->constrained('document_versions')->nullOnDelete();
        });

        // Migrate Data
        $documents = DB::table('documents')->get();

        foreach ($documents as $doc) {
            $versionId = DB::table('document_versions')->insertGetId([
                'document_id' => $doc->id,
                'version_number' => 1,
                'title' => $doc->title,
                'description' => $doc->description,
                'document_state_id' => $doc->document_state_id,
                'created_at' => $doc->created_at,
                'updated_at' => $doc->updated_at,
                'deleted_at' => $doc->deleted_at,
            ]);

            DB::table('documents')->where('id', $doc->id)->update([
                'current_version_id' => $versionId,
            ]);

            // Migrate Media
            DB::table('media')
                ->where('model_type', 'App\Models\Document')
                ->where('model_id', $doc->id)
                ->update([
                    'model_type' => 'App\Models\DocumentVersion',
                    'model_id' => $versionId,
                ]);

            // Migrate Activity Logs
            DB::table('activity_log')
                ->where('subject_type', 'App\Models\Document')
                ->where('subject_id', $doc->id)
                ->update([
                    'subject_type' => 'App\Models\DocumentVersion',
                    'subject_id' => $versionId,
                ]);
        }

        // Drop old columns from documents
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropForeign(['document_state_id']);
                $table->dropColumn(['title', 'description', 'document_state_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('document_state_id')->nullable()->constrained('document_states');
        });

        // Reverse data migration
        $versions = DB::table('document_versions')->where('version_number', 1)->get();

        foreach ($versions as $version) {
            DB::table('documents')->where('id', $version->document_id)->update([
                'title' => $version->title,
                'description' => $version->description,
                'document_state_id' => $version->document_state_id,
            ]);

            // Migrate Media back
            DB::table('media')
                ->where('model_type', 'App\Models\DocumentVersion')
                ->where('model_id', $version->id)
                ->update([
                    'model_type' => 'App\Models\Document',
                    'model_id' => $version->document_id,
                ]);

            // Migrate Activity Logs back
            DB::table('activity_log')
                ->where('subject_type', 'App\Models\DocumentVersion')
                ->where('subject_id', $version->id)
                ->update([
                    'subject_type' => 'App\Models\Document',
                    'subject_id' => $version->document_id,
                ]);
        }

        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['current_version_id']);
            $table->dropColumn('current_version_id');
        });

        Schema::dropIfExists('document_versions');
    }
};
