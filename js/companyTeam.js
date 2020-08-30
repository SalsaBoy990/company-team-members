jQuery(document).ready(function ($) {
  // only send AJAX request when this div is added
  if ($("#ag-company-team-table").length > 0) {
    var data = {
      action: "ag_company_team_action",
      security: AGCompanyTeamAJAX.security,
      args: {
        'type':             'table',
        'name':             1,
        'first_name_first': 0,
        'photo':            1,
        'phone':            0,
        'email':            1,
        'position':         1,
        'department':       0,
        'works_since':      0
      }
    };

    $.ajax({
      type: "POST",
      url: AGCompanyTeamAJAX.ajaxurl,
      data: data,
    })
      .done(function ($response) {
        console.log("AG Company Team AJAX - OK response.");
        $("#ag-company-team-table").html($response.data);
      })
      .fail(function () {
        console.log("AG Company Team AJAX res error.");
        $("#ag-company-team-table").html(
          "AG Company Team AJAX response error."
        );
      })
      .always(function () {
        console.log("AG Company Team AJAX finished.");
      });
  }
});
