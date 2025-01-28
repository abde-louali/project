<?php
session_start(); 
require_once '../Controller/AdminController.php';  

header('Content-Type: application/json');  

// Enable error display for debugging
ini_set('display_errors', 1); 
error_reporting(E_ALL);  

// Extensive logging
error_log("======= START create_folders.php =======");
error_log("SESSION: " . print_r($_SESSION, true));
error_log("POST DATA: " . print_r($_POST, true));

// Session verification
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {     
   error_log("SESSION ERROR: Unauthorized access");
   echo json_encode([         
       'success' => false,         
       'message' => 'Accès non autorisé',         
       'details' => ['errors' => ['Session expirée ou utilisateur non autorisé']]     
   ]);     
   exit(); 
}  

// POST parameters check
if (!isset($_POST['class']) || !isset($_POST['filiere'])) {     
   error_log("ERROR: Missing required parameters");
   echo json_encode([         
       'success' => false,         
       'message' => 'Paramètres manquants',         
       'details' => ['errors' => ['La classe et la filière sont requises']]     
   ]);     
   exit(); 
}  


try {     
   $adminController = new AdminController();          

   $class = trim($_POST['class']);     
   $filiere = trim($_POST['filiere']);          

   error_log("Creating folders - Class: $class, Filiere: $filiere");          

   // Create student folders
   $result = $adminController->model->createStudentFolders($class, $filiere);          

   error_log("Folder Creation Result: " . print_r($result, true));          

   // Prepare response
   $response = [         
       'success' => !empty($result['success']) || !empty($result['updated']),         
       'message' => !empty($result['success']) || !empty($result['updated'])              
           ? 'Dossiers créés avec succès'              
           : 'Aucun dossier créé',         
       'details' => [             
           'success' => $result['success'] ?? [],             
           'errors' => $result['errors'] ?? [],             
           'updated' => $result['updated'] ?? []         
       ]     
   ];          

   // If no success and errors exist, mark as failure
   if (empty($result['success']) && empty($result['updated']) && !empty($result['errors'])) {         
       $response['success'] = false;         
       $response['message'] = 'Erreur lors de la création des dossiers';     
   }          

   // If folders created successfully, send path
   if ($response['success']) {
       $uploadsPath = realpath('uploads/' . $filiere . '/' . $class);
       
       // Extensive logging of uploads path
       error_log("Uploads Path: $uploadsPath");
       
       // Prepare cURL request
       $ch = curl_init('http://localhost/flasktest.php');
       curl_setopt($ch, CURLOPT_POST, 1);
       curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
           'uploads_path' => $uploadsPath,
           'filiere' => $filiere,
           'class' => $class
       ]));
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       
       $flaskResponse = curl_exec($ch);
       
       if(curl_errno($ch)){
           error_log('CURL Error: ' . curl_error($ch));
           $response['flask_communication'] = false;
       } else {
           error_log('Flask Response: ' . $flaskResponse);
           $response['flask_communication'] = true;
       }
       
       curl_close($ch);
   }

   error_log("Final Response: " . print_r($response, true));
   echo json_encode($response);      

} catch (Exception $e) {     
   error_log("EXCEPTION: " . $e->getMessage());
   echo json_encode([         
       'success' => false,         
       'message' => 'Erreur lors de la création des dossiers',         
       'details' => [             
           'errors' => [$e->getMessage()]         
       ]     
   ]); 
} 

error_log("======= END create_folders.php =======");
?>