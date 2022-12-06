$(function () {
    $("#tmenulist").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": [{
            "text": '<i class="fa fa-fw fa-plus-circle"></i> Add record',
            "action": function ( e, dt, node, config ) {
                window.location.href = site_url + "uac/master/menu/add";
            }
        }]
    }).buttons().container().appendTo('#tmenulist_wrapper .col-md-6:eq(0)');

    $('#tmenulist tbody').on('click', 'button#delete', function () {
        var id = $(this).attr("data-id");
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this data!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                del(id);
            } else {
                swal("", "Menu canceled for deletion.", "info").then((value) => {
                    window.location.href = site_url + "uac/master/menu";
                });
            }
        });
    });
});

function del(id)
{
    $.ajax({
        url: site_url + "uac/master/menu/delete/" + id,
        type: "POST",
        dataType: "json",
        success: function(response) {
            console.log(response);
            if(response.status == 1) {
                swal("", response.messages, response.icon).then((value) => {
                    window.location.href = site_url + response.url;
                });
            } else {
                swal("", response.messages, response.icon);
            }
        },
        error: function (e) {
            console.log("Terjadi kesalahan pada sistem");
            swal("", "Terjadi kesalahan pada sistem.", "error");
        }        
    });
}