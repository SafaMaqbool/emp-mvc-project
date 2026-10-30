<?php include 'views/templates/header.html.php'; ?>
<?php include 'views/templates/navbar.html.php'; ?>

<?php if (!empty($success_message)): ?>
    <div style="background:#d4edda; color:#155724; padding:10px; margin:10px 0; border-radius:4px;">
        <?php echo htmlspecialchars($success_message); ?>
    </div>
<?php endif; ?>

<?php if (!empty($error_message)): ?>
    <div style="background:#f8d7da; color:#721c24; padding:10px; margin:10px 0; border-radius:4px;">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
<?php endif; ?>

<?php if (!empty($skipReasons)): ?>
    <div style="background:#fff3cd; color:#856404; padding:10px; margin:10px 0; border-radius:4px;">
        <strong>Why rows were skipped:</strong>
        <ul>
            <?php foreach ($skipReasons as $reason): ?>
                <li><?php echo htmlspecialchars($reason); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<h3>Batch CSV Import</h3>

<form action="index.php?action=import_process" method="POST" enctype="multipart/form-data" class="form">
    <div class="form-group">
        <label for="csv_file">Select CSV File: </label>
        <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
        <small style="display:block; margin-top:5px; color:#666;">CSV Format: Firstname, Surname, DeptNo, Office, Salary, NiNo</small>
    </div>

    <button type="submit">Upload and Import</button>
</form>

<p><a href="index.php?action=list">Back to Employee List</a></p>

<?php include 'views/templates/footer.html.php'; ?>