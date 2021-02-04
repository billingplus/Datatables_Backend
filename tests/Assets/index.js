$(document).ready(function () {
  $("#datatable").DataTable({
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    ajax: {
      url: "../Controllers/Controller.php/Datatables",
      type: "POST",
      data: function (d) {
        d.status = $("#filter").val();
      },
      /*complete: data => {
        console.log(data.responseText)
      }*/
    },
    columns: [
      {
        data: "id",
      },
      {
        data: "name",
      },
      {
        data: "description",
      },
      {
        data: "id",
      },
    ],
  });
});
