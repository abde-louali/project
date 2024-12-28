<?php
// class Conn {
//     $host='localhost';
//     $dbname = 'ISTA_project';
//     $user ='root';
//     $pass = '';
//     public function __construct(){

//         try{
//             $this->conn = new PDO("mysql:host=".$this->host.";dbname=".$this->dbname,$this->user,$this->pass);
//             $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//         }catch(PDOException $e){
//             echo "Connection failed: ".$e->getMessage();
//         }

//     }
// }

// class Login extends Conn{
//     public function login($v1,$v2){
//         try{
//             $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//             $query = "SELECT cin , class_name from class WHERE cin = $v1 and cin = $v2" 
//             $stat = $db->query($query);
//             $rows = $stat->fetch(PDO::FETCH_ASSOC);
//             return $rows;
//         }catch (PDOException){
//              echo $e->getMessage();
//         }

//     }
// }

function conn(){
    $host = "localhost";
    $dbname = "ISTA_project";
    $user = "root";
    $pass = "";
    $dbn = new PDO("mysql:host=$host;dbname=$dbname",$user,$pass);
    $dbn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    return $dbn;
}

function Login($v1,$v2){
    $db = conn();
    $query = "SELECT cin , code_class FROM classes where cin ='$v1' and cin = '$v2'";
    $stat = $db->query($query);
    $rows = $stat->fetch(PDO::FETCH_ASSOC);
    return $rows;
}
function Username($v1){
    $db = conn();
    $query = "SELECT s_fname  FROM classes where cin ='$v1'";
    $stat = $db->query($query);
    $rows = $stat->fetch(PDO::FETCH_ASSOC);
    return $rows;
}

    function Insertdata($v1, $v2, $v3, $v4, $v5) {
        try {
            $db = conn(); 
            
            $query = "INSERT INTO student (cin, code_class, bac_img, id_card_img, birth_img)
                      SELECT cin, code_class, ?, ?, ?
                      FROM classes
                      WHERE cin = ? AND code_class = ?";
    
            $stat = $db->prepare($query);
            
          
            $inserted = $stat->execute([$v3, $v4, $v5, $v1, $v2]);
    
            return $inserted;
        } catch (PDOException $e) {

            error_log("Error in Insertdata: " . $e->getMessage());
            return false;
        }
    }
    