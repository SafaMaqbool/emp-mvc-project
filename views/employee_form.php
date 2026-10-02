<?php include 'views/templates/header.html.php'; ?>
<?php include 'views/templates/navbar.html.php';?>

<h3>Register new employee</h3>

<form action="index.php?action=store" method="POST" class="form">
    <div class="form-group">
        <label for="EmpNo">Employee Number (EmpNo):</label>
        <input type="text" name="EmpNo" id="EmpNo" placeholder="eg: E10" required>

    </div>

    <div class="form-group">
        <label for="NiNo">NI Number (NiNo)</label>
        <input type="text" name="NiNo" id="NiNo" placeholder="eg: NX123456A" required>
    </div>

    <div class="form-group">
        <label for="Firstname">First Name: </label>
        <input type="text" name="Firstname" id="Firstname" required>

    </div>

    <div class="form-group">
        <label for="Surname">Surname: </label>
        <input type="text" name="Surname" id="Surname" required>
    </div>
    
    <div class="form-group">
        <label for="DeptNo">Department: </label>
        <select name="DeptNo" id="DeptNo">
            <option value="">Select Department</option>
            <option value="D1">Software Engineering (D1)</option>
            <option value="D2">Design (D2)</option>
            <option value="D3">Marketing (D3)</option>
        </select>
    </div>

    <div class="form-group">
        <label>Office Room:</label>
        <input type="text" name="Office" placeholder="e.g. R105" required>
    </div>

    <div class="form-group">
        <label for="Salary">Annual Salary: </label>
        <input type="number" name="Salary" placeholder="eg:50000.00" required>
    </div>

    <button type="submit">Save Employee</button>


</form>



<?php include 'views/templates/footer.html.php'?>