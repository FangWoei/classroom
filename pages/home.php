<?php 

session_start();

$database = new PDO(
    'mysql:host=devkinsta_db;dbname=classroom',
    'root',
    'WaoDc0cvoNR1eUiM'
);

$query = $database->prepare('SELECT * FROM students');
$query->execute();

$students = $query->fetchAll();

if ( !isset($_SESSION['add_form_csrf_token']) ){
  $_SESSION['add_form_csrf_token'] = bin2hex( random_bytes(32));
}
// var_dump ( $_SESSION['add_form_csrf_token']);
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'){
    if ( $_POST['add_form_csrf_token'] !== $_SESSION['add_form_csrf_token'] )
    {
      die("Nice try! But I'm smarter than you!");
    }
    unset( $_SESSION['add_form_csrf_token'] );

    if($_POST['action'] ==='add'){
        
    $statement = $database->prepare(
        "INSERT INTO students (`name`) VALUES (:name)"
    );
    $statement->execute([
        'name' => $_POST['student']
    ]);
    header('Location:/');
    exit;
    }

    if($_POST['action'] === 'remove' ) {
        $statement = $database->prepare(
            'DELETE FROM students WHERE id = :id'
        );
        $statement->execute([
          'id' => $_POST['id']
      ]);
        header('Location:/');
        exit;
    }
  }

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Classroom</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65"
      crossorigin="anonymous"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css"
    />
    <style type="text/css">
      body {
        background: #f1f1f1;
      }
    </style>
  </head>
  <body>
    <div class="card rounded shadow-sm mx-auto my-4" style="max-width: 500px;">
      <div class="card-body">
        <div class="d-flex">
            <h3>My Classroom</h3>
            <?php if ( isset($_SESSION['user'])): ?>
                <a href="/logout" class="btn btn-link" id="logout">Logout</a>
                <?php else : ?>
          <a href="/login" class="btn btn-link" id="login">Login</a>
          <a href="/signup" class="btn btn-link" id="signup">Sign Up</a>
          <?php endif; ?>
        </div>
        <?php if ( isset($_SESSION['user'])): ?>
          <form method="POST" action="<?php echo $_SERVER['REQUEST_URI'];?>" class=" d-flex justify-content-between align-items-center">
                <input
                type="text"
                class="form-control"
                placeholder="Add a new student"
                name="student"
                required
                />
                <input 
                type="hidden"
                name="action"
                value="add"
                />
                <button class="btn btn-primary btn-sm rounded ms-2">
                  Add
                </button>
                <input 
                type="hidden"
                name="add_form_csrf_token"
                value="<?php echo $_SESSION['add_form_csrf_token']; ?>"
                />
            </form>
            <?php else : ?>
          <?php endif; ?>
      </div>
    </div>

    <div class="card rounded shadow-sm mx-auto my-4" style="max-width: 500px;">
      <div class="card-body">
        <h3 class="card-title mb-3">Student</h3>
        <div class="mt-4">
            <?php foreach ( $students as $student ) : ?>
                <div class="d-flex justify-content-between gap-3 mb-3">
                    <?php echo $student['name']; ?>
                    <?php if ( isset($_SESSION['user'])): ?>
                    <form method="POST" action="<?php echo $_SERVER ['REQUEST_URI']; ?>">
                    <input 
                        type="hidden"
                        name="id"
                        value="<?php echo $student['id']; ?>"
                        />
                    <input 
                        type="hidden"
                        name="action"
                        value="remove"
                        />
                        <button class="btn btn-danger btn-sm">
                          Remove
                        </button>
                        <input 
                        type="hidden"
                        name="add_form_csrf_token"
                        value="<?php echo $_SESSION['add_form_csrf_token']; ?>"
                        />
                      </form>
                    <?php else : ?>
                    <?php endif; ?>
                </div>
            <?php endforeach ?>
        </div>
      </div>
    </div>


    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
      crossorigin="anonymous"
    ></script>
  </body>
</html>
