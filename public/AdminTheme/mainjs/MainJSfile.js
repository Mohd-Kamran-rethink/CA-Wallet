const managerModal=(id)=>{
    $('#deleteInput').val(id)
    $(`#modal-default`).modal('show');
    alert(id)
}