<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet"><?php $user = '';
echo '<table class="table table-bordered">
    <thead>
        <tr>
            <th>User</th>
            <th>Type</th>
            <th>Area</th>
            <th>Days Count</th>
            <th>Total Workhours</th>
        </tr>
    </thead>
    <tbody>';

foreach ($results as $row) {
    if ($user !== $row->User) {
        $user = $row->User;
        $user_rowspan = count($results->where('User', $user));
        echo "<tr><td rowspan=\"$user_rowspan\">$user</td>";
    } else {
        echo "<tr>";
    }
    echo "<td>{$row->Type}</td><td>{$row->Area}</td><td>{$row->Count}</td></tr>";
}

echo '</tbody>
</table>';?>