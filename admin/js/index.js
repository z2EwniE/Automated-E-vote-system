$(document).on('click', '.view-votes', function() {
    let student_id = $(this).data('id');

    $.ajax({
        type: 'POST',
        url: 'process/view-votes-by-id.php',
        data: {
            action: 1,
            student_id: student_id
        },
        success: function(res) {
            var res = JSON.parse(res);
            const modal = new bootstrap.Modal("#view-votes-by-id");

            if (res.success) {
                // Clear the previous table content
                $('#view-votes-by-id .table tbody').empty();

                // Loop through each voting record and add to the table
                res.data.forEach(function(vote) {
                    let row = `
                        <tr>
                            <td>${vote.position_name}</td>
                            <td>${vote.candidate_name}</td>
                            <td>${vote.partylist_name}</td>
                            
                        </tr>
                    `;
                    $('#view-votes-by-id .table tbody').append(row);
                });

                // Show the modal with updated data
                modal.show();
            }
        }
    });
});
