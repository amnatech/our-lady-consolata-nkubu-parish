<script>
    export let tithes=[];
</script>

<main>
    <table class="ui striped unstackable compact table">
        <thead>
            <tr>
                <th>No</th>
                <th>Serial</th>
                <th>Name</th>
                <th>House</th>
                <th>Amount</th>
                <th>Month</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            {#each tithes as t,i}
                <tr>
                    <td>{i+1}</td>
                    <td>{t.serial_no}</td>
                    <td>{t.name}</td>
                    <td>{t.house}</td>
                    <td>{t.amount}</td>
                    <td>{t.month}</td>
                    <td>{t.created_at}</td>
                    <td>
                        View
                    </td>
                </tr>
            {/each}
        </tbody>
    </table>
</main>