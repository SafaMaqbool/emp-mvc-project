<?php include 'views/templates/header.html.php'; ?>
<?php include 'views/templates/navbar.html.php'; ?>


<?php if (isset($error_message) && !empty($error_message)): ?>
    <div style="color: red; background: #fee; padding: 10px; border: 1px solid red; margin-bottom: 15px;">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
<?php endif; ?>
<h3>Register new employee</h3>

<form action="index.php?action=store" method="POST" class="form">
    <div class="form-group">
        <label for="EmpNo">Employee Number (EmpNo):</label>

        <input type="text" id="EmpNo" disabled readonly value="<?= htmlspecialchars($autoEmpNo ?? '');  ?>">
        <input type="hidden" name="EmpNo" value="<?= htmlspecialchars($autoEmpNo ?? ''); ?>">

    </div>

    <div class="form-group">
        <label for="NiNo">NI Number (NiNo)</label>
        <input type="text" name="NiNo" id="NiNo" placeholder="eg: NX123456A" value="<?= htmlspecialchars($formData['NiNo'] ?? ''); ?>" required>

        <div class="form-group">
            <label for="Firstname">First Name: </label>
            <input type="text" name="Firstname" id="Firstname" value="<?= htmlspecialchars($formData['FirstName'] ?? '');  ?>" required>

        </div>

        <div class="form-group">
            <label for="Surname">Surname: </label>
            <input type="text" name="Surname" id="Surname" value="<?= htmlspecialchars($formData['Surname'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="DeptNo">Department: </label>
            <select name="DeptNo" id="DeptNo">
                <option value="">Select Department</option>
                <option value="D1" <?= (isset($formData['DeptNo']) && $formData['DeptNo'] == 'D1') ? 'selected' : ''; ?>>Software Engineering (D1)</option>
                <option value="D2" <?= (isset($formData['DeptNo']) && $formData['DeptNo'] == 'D2') ? 'selected' : ''; ?>>Design (D2)</option>
                <option value="D3" <?= (isset($formData['DeptNo']) && $formData['DeptNo'] == 'D3') ? 'selected' : '';  ?>>Marketing (D3)</option>
            </select>
        </div>

        <div class="form-group">
            <label>Office Room:</label>
            <input type="text" name="Office" placeholder="e.g. R105" value="<?= htmlspecialchars($formData['Office'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="Salary">Annual Salary: </label>
            <input type="number" name="Salary" placeholder="eg:50000.00" value="<?= htmlspecialchars($formData['Salary'] ?? ''); ?>" required>
        </div>

        <button type="submit">Save Employee</button>

    </div>
</form>



<?php include 'views/templates/footer.html.php' ?>