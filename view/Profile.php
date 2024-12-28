<?php 
session_start();
if (!isset($_SESSION['username'])) {
    header('location:Login.php');
    exit;
} else {
    include "../Model/conx.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Submission</title>
</head>
<body>
<?php 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $errors = [];
    $cin = $_POST['cin'] ?? '';
    $grp = $_POST['grp'] ?? '';
    if (empty($cin)) {
        $errors[] = "CIN is required.";
    }
    if (empty($grp)) {
        $errors[] = "Group is required.";
    }
    $uploadedFiles = [];
    $fileFields = ['bac', 'cin_pic', 'brth'];
    foreach ($fileFields as $field) {
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES[$field]['tmp_name'];
            $fileContent = file_get_contents($fileTmpPath);
            $uploadedFiles[$field] = $fileContent;
        } else {
            $errors[] = ucfirst($field) . " file is required.";
        }
    }
    if (empty($errors)) {
        $insert = Insertdata($cin, $grp, $uploadedFiles['bac'], $uploadedFiles['cin_pic'], $uploadedFiles['brth']);
        if ($insert) {
            echo "<p>Data successfully inserted!</p>";
        } else {
            echo "<p>Failed to insert data. Please try again.</p>";
        }
    } else {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
}
?>
    <h1>Hello</h1> 
    
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="text1">CIN:</label>
        <input type="text"  name="cin" required><br><br>

        <label for="text2">Group:</label>
        <input type="text"  name="grp" required><br><br>

        <label for="file1">Bac Image:</label>
        <input type="file"  name="bac" required><br><br>

        <label for="file2">CIN Image:</label>
        <input type="file"  name="cin_pic" required><br><br>

        <label for="file3">Birth Certificate Image:</label>
        <input type="file"  name="brth" required><br><br>

        <button type="submit">Submit</button>
    </form>
</body>
</html>
<?php } ?>
