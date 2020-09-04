< script >
    function deleteUser(id) {

        if (confirm('Estas seguro de eliminar este registro?')) {

            $('#tr-' + id).hide();

            $.ajax({
                type: 'POST',
                url: "/user/delete/" + id,
                data: {
                    '_token': $('input[name=_token]').val(),
                },
                success: function (data) {

                    alert(data);

                }
            });
        }
    } 
</script>