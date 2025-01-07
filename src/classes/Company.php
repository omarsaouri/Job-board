<?php
    
    require_once __DIR__ . '/../config/supabase.php';


    Class Company {
        private $supabase;

        public function __construct() {
            $this->supabase = new SupabaseConfig();
        }

        public function getCompanies() {
            try {
                return $this->supabase->query(
                    '/rest/v1/companies',
                    'GET'
                );
            } catch (Exception $e) {
                error_log('Error fetching companies: '. $e->getMessage());
                throw $e;
            }
        }

        public function getCompanyById($companyId) {
            try {
                return $this->supabase->query(
                    "/rest/v1/companies?id=eq.{$companyId}",
                    'GET'
                );
            } catch (Exception $e) {
                error_log('Error fetching company: '. $e->getMessage());
                throw $e;
            }
        }

        public function createCompany($userId, $name, $description, $location, $website) {
            try {
                $response = $this->supabase->query(
                    "/rest/v1/companies",
                    'POST',
                    [
                        'user_id' => $userId,
                        'name' => $name,
                        'description' => $description,
                        'location' => $location,
                        'website' => $website,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ],
                    ['Prefer: return=representation'] 
                );
        
                error_log('Supabase response: ' . print_r($response, true));
        
                // Check if the response contains the newly created company data
                if (!empty($response)) {
                    return $response;  // Return the newly created company record
                }
        
                // Log the error and throw an exception if no response is returned
                error_log('Error: No valid response from Supabase.');
                throw new Exception('Failed to create company. No response from API.');
                
            } catch (Exception $e) {
                error_log('Error creating company: '. $e->getMessage());
                throw $e;
            }
        }

        public function getCompanyByUserId($userId) {
            try {
                $response = $this->supabase->query(
                    '/rest/v1/companies',
                    'GET',
                    null,
                    ['Range: 0-0', 'Prefer: count=exact', "Select: id,name,description,location,website,user_id"]
                );
    
                // Supabase uses PostgREST so we need to specify our query in the URL
                $endpoint = "/rest/v1/companies?user_id=eq." . urlencode($userId) . "&limit=1";
                $result = $this->supabase->query($endpoint);
                
                // Supabase returns an array, even for single results
                return !empty($result) ? $result[0] : false;
    
            } catch (Exception $e) {
                error_log("Error getting company: " . $e->getMessage());
                return false;
            }
        }
               
    }

?>