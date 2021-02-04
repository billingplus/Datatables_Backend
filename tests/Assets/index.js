$(document).ready(function () {
  $("#datatable").DataTable({
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    ajax: {
      url: "Controller/Datatables",
      type: "POST",
      data: function (d) {
        d.status = $("#filter").val();
      },
    },
    columns: [
      {
        data: "id",
      },
      {
        data: "name",
      },
      {
        data: "email",
      },
      {
        data: "id",
      },
    ],
  });
});
