<?php
require_once __DIR__ . '/../config/supabase.php';

class Application {
    private $supabase;

    public function __construct() {
        $this->supabase = new SupabaseConfig();
    }

    public function createApplication($data) {
        try {
            $applicationData = [
                'job_id' => $data['job_id'],
                'user_id' => $data['user_id'],
                'status' => 'pending',
                'resume_url' => $data['resume_url'] ?? '',
                'cover_letter' => $data['cover_letter'] ?? null
            ];

            return $this->supabase->query(
                '/rest/v1/applications',
                'POST',
                $applicationData,
                ['Prefer: return=minimal']
            );
        } catch (Exception $e) {
            error_log('Error creating application: ' . $e->getMessage());
            throw $e;
        }
    }

    public function hasUserApplied($userId, $jobId) {
        try {
            $response = $this->supabase->query(
                "/rest/v1/applications?user_id=eq.{$userId}&job_id=eq.{$jobId}",
                'GET'
            );
            return !empty($response);
        } catch (Exception $e) {
            error_log('Error checking application: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getUserApplications($userId) {
        try {
            return $this->supabase->query(
                "/rest/v1/applications?select=*,jobs!inner(title,companies(name))&user_id=eq.{$userId}&order=created_at.desc",
                'GET'
            );
        } catch (Exception $e) {
            error_log('Error fetching applications: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getApplicationsByJobId($jobId) {
        try {
            return $this->supabase->query(
                "/rest/v1/applications?select=*,profiles:user_id(username,role)&job_id=eq.{$jobId}&order=created_at.desc",
                'GET'
            );
        } catch (Exception $e) {
            error_log('Error fetching applications: ' . $e->getMessage());
            return [];
        }
    }

    public function updateStatus($applicationId, $newStatus) {
        try {
            return $this->supabase->query(
                "/rest/v1/applications?id=eq.{$applicationId}",
                'PATCH',
                ['status' => $newStatus],
                ['Prefer: return=minimal']
            );
        } catch (Exception $e) {
            error_log('Error updating application status: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getApplicationCount($jobId) {
        try {
            $response = $this->supabase->query(
                "/rest/v1/applications?job_id=eq.{$jobId}&select=count",
                'GET'
            );
            return $response[0]['count'] ?? 0;
        } catch (Exception $e) {
            error_log('Error counting applications: ' . $e->getMessage());
            return 0;
        }
    }
}
?>