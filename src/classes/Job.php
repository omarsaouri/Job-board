<?php
    // classes/Job.php
    require_once __DIR__ . '/../config/supabase.php';

    class Job {
        private $supabase;

        public function __construct() {
            $this->supabase = new SupabaseConfig();
        }

        public function createJob($data) {
            try {
                $jobData = [
                    'company_id' => $data['company_id'],
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'requirements' => $data['requirements'],
                    'salary_range' => $data['salary_range'],
                    'location' => $data['location'],
                    'type' => $data['type'] 
                ];

                $response = $this->supabase->query(
                    '/rest/v1/jobs',
                    'POST',
                    $jobData,
                    ['Prefer: return=minimal']
                );

                return true;
            } catch (Exception $e) {
                error_log('Error creating job: ' . $e->getMessage());
                throw $e;
            }
        }

        public function getJobsByEmployer($companyId) {
            try {
                return $this->supabase->query(
                    "/rest/v1/jobs?company_id=eq.{$companyId}&order=created_at.desc",
                    'GET'
                );
            } catch (Exception $e) {
                error_log('Error fetching jobs: ' . $e->getMessage());
                throw $e;
            }
        }

        // Add these methods to your existing Job class in classes/Job.php

        public function getJobById($jobId) {
            try {
                $response = $this->supabase->query(
                    "/rest/v1/jobs?id=eq.{$jobId}",
                    'GET'
                );
                
                return !empty($response) ? $response[0] : null;
            } catch (Exception $e) {
                error_log('Error fetching job: ' . $e->getMessage());
                throw $e;
            }
        }

        public function updateJob($data) {
            try {
                $jobId = $data['id'];
                unset($data['id']); // Remove id from update data
                
                $response = $this->supabase->query(
                    "/rest/v1/jobs?id=eq.{$jobId}",
                    'PATCH',
                    $data,
                    ['Prefer: return=minimal']
                );
                
                return true;
            } catch (Exception $e) {
                error_log('Error updating job: ' . $e->getMessage());
                throw $e;
            }
        }

        public function deleteJob($jobId) {
            try {
                $response = $this->supabase->query(
                    "/rest/v1/jobs?id=eq.{$jobId}",
                    'DELETE',
                    null,
                    ['Prefer: return=minimal']
                );
                
                return true;
            } catch (Exception $e) {
                error_log('Error deleting job: ' . $e->getMessage());
                throw $e;
            }
        }

        public function getRecentJobs($limit = 10) {
            try {
                $response = $this->supabase->query(
                    "/rest/v1/jobs?select=*,companies(name)&status=eq.active&order=created_at.desc&limit={$limit}",
                    'GET'
                );
                
                // Transform response to match expected format
                return array_map(function($job) {
                    $job['company_name'] = $job['companies']['name'];
                    unset($job['companies']);
                    return $job;
                }, $response);
            } catch (Exception $e) {
                error_log('Error fetching recent jobs: ' . $e->getMessage());
                throw $e;
            }
        }


        public function searchJobs($query = '', $location = '', $type = '') {
            try {
                $endpoint = "/rest/v1/jobs?select=*,companies(name)&status=eq.active";
                
                if ($query) {
                    $endpoint .= "&or=(title.ilike.*" . urlencode($query) . "*,description.ilike.*" . urlencode($query) . "*)";
                }
                if ($location) {
                    $endpoint .= "&location.ilike.*" . urlencode($location) . "*";
                }
                if ($type) {
                    $endpoint .= "&type=eq." . urlencode($type);
                }
                
                $endpoint .= '&order=created_at.desc';
                
                return $this->supabase->query($endpoint, 'GET');
            } catch (Exception $e) {
                error_log('Search error: ' . $e->getMessage());
                return [];
            }
        }

    }

?>