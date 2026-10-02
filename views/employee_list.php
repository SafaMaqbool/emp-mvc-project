<?php include 'views/templates/header.html.php'?>
<?php include 'views/templates/navbar.html.php'?>

<h3>Employee Directory</h3>
<table>
    <thead>
        <tr>
            <th>EmpNo</th>
            <th>NI Number</th>
            <th>Full Name</th>
            <th>Department</th>
            <th>Office</th>
            <th>Annual Salary</th>

        </tr>
        
    </thead>
    <tbody>
        <?php if(!empty($employees)): ?>
            <!-- table rows  -->
             <?php foreach($employees as $emp):?>

                <tr>
                    <td><?php echo htmlspecialchars($emp['EmpNo']);?></td>
                    <td><?php echo htmlspecialchars($emp['NiNo']);?></td>
                    <td><?php echo htmlspecialchars($emp['Firstname'] .' '. $emp['Surname']);?></td>
                    <td><?php echo htmlspecialchars($emp['DeptName']);?></td>
                    <td><?php echo htmlspecialchars($emp['Office']);?></td>
                    <td><?php echo htmlspecialchars($emp['Salary']);?></td>

                </tr>

              <?php endforeach;?>  
        
        
        <?php else: ?> 
            <!-- msg telling k there is no data to show  -->
             <tr><td colspan="6">No employee records found</td></tr>

        <?php endif;?>

    </tbody>
</table>

<?php include 'views/templates/footer.html.php'?>