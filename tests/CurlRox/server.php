<?php header('Content-type: application/json'); ?>

<?php if (isset($_GET['test'])): ?>
    <h2>Test server</h2>
    <hr>
    <p>
        <a href="http://fake-content-a.org">Test Link 1</a>
        <a href="http://fake-content-b.org">Test Link 2</a>
        <a href="http://fake-content-c.org">Test Link 3</a>
        <a href="http://fake-content-d.org">Test Link 4</a>
    </p>

<?php elseif(isset($_POST['test'])):
    header('Content-type: application/json');
    echo json_encode($_POST);

    ?>

<?php endif;
