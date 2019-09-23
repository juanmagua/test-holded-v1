// The root URL for the RESTful services
var rootURL = "http://localhost:8080/";

var currentWidget;

var currentList = [];

var $token = localStorage.getItem("token");

var $username = localStorage.getItem("username");
;

console.log($token, $username);

$(document).ready(function () {
    console.log("ready!");

    init();

    $('#btn-login').click(function () {
        login();
        return false;
    });

    // OPEN MODAL
    $('#add-widget').click(function () {
        cleanForm();
        $('#modal-widget').modal('show');
        $('#btnSave').text('Create');
        $('#btnDelete').hide();

        return false;
    });

    // BTN CREATE O UPDATE
    $('#btnSave').click(function () {
        if ($('#widget-id').val() == '') {
            createWidget();
        } else {
            updateWidget();
        }
        return false;
    });

    $('#btnDelete').click(function () {
        if (confirm("Are you sure?")) {
            deleteWidget();
        }
        return false;
    });

    $('body').on('click', '.edit-widget', function () {

        var $widget = findById($(this).attr('id'));

        cleanForm();

        renderDetails($widget);

        $('#btnDelete').show();

        $('#btnSave').text('Update');

        $('#modal-widget').modal('show');

        return false;

    });

});


function init() {

    console.log($token);

    if ($token !== 'undefined' && $token !== null) {
        $("#dashboard").show();
        $("#login").hide();
        $(".blog-header-logo").html("Hey," + $username);
        findAll();
    } else {
        $("#login").show();
        $("#dashboard").hide();
    }
}

function login() {
    console.log('login..');
    $.ajax({
        type: 'POST',
        contentType: 'application/json',
        url: rootURL + 'login',
        dataType: "json",
        data: loginFormToJSON(),
        success: function (data, textStatus, jqXHR) {
            localStorage.setItem("token", data.user.token);
            localStorage.setItem("username", data.user.username);
            $token = data.user.token;
            $username = data.user.username;
            init();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert("Login Failed");
        }
    });
}

function logout() {
    localStorage.clear();
    $("#login").show();
    $("#dashboard").hide();
}

function findAll() {
    console.log('findAll');
    $.ajax({
        type: 'GET',
        url: rootURL + 'widgets',
        dataType: "json", // data type of response
        headers: {
            Authorization: localStorage.getItem("token")
        },
        success: renderList,
        error: function (jqXHR, textStatus, errorThrown) {
            var responseText = jqXHR.responseText;
            var responseTextAsAnObject = JSON.parse(responseText);
            alert(responseTextAsAnObject.message);
            logout();
        }
    });
}

/*
 function newWidget() {
 $('#btnDelete').hide();
 currentWine = {};
 renderDetails(currentWine); // Display empty form
 }*/

function findById(id) {
    var _obj = null;
    $.each(currentList, function (index, obj) {
        if (obj.id == id) { // id.toString() if it is intso
            _obj = obj;
            return false;
        }
    });
    return _obj;
}


function _update(widget) {
    var _obj = null;
    $.each(currentList, function (index, obj) {
        if (obj.id == widget.id) { // id.toString() if it is intso
            currentList[index] = widget;
            return false;
        }
    });
    return _obj;
}

function createWidget() {

    console.log("err" + validateForm());

    if (validateForm()) {
        return false;
    }


    console.log('createWidget');
    $.ajax({
        type: 'POST',
        contentType: 'application/json',
        url: rootURL + 'widgets',
        dataType: "json",
        data: formToJSON(),
        headers: {
            Authorization: $token
        },
        success: function (data, textStatus, jqXHR) {

            currentList.push(data.widget);

            renderList(currentList);

            $('#modal-widget').modal('hide');

        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(xjqXHRhr.responseText);
        }
    });
}

function updateWidget() {
    console.log('updateWidget');
    $.ajax({
        type: 'PUT',
        contentType: 'application/json',
        url: rootURL + 'widgets',
        dataType: "json",
        data: formToJSON(),
        headers: {
            Authorization: localStorage.getItem("token")
        },
        success: function (data, textStatus, jqXHR) {

            _update(data.widget);

            renderList(currentList);

            $('#modal-widget').modal('hide');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(alert(xjqXHRhr.responseText));
        }
    });
}

function deleteWidget() {
    console.log('deleteWidget');
    $.ajax({
        type: 'DELETE',
        url: rootURL + 'widgets/' + $('#widget-id').val(),
        success: function (data, textStatus, jqXHR) {


            alert('Widget deleted successfully');
            
            console.log(currentList.length);
            
            RemoveWidgetList($('#widget-id').val());

            renderList(currentList);
            
            console.log(currentList.length);
            

            $('#modal-widget').modal('hide');

        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR.responseText);
        }
    });
}

function RemoveWidgetList(id) {

    $.each(currentList, function (index, obj) {
        if (obj.id == id) { // id.toString() if it is intso

            currentList.splice(index, 1);

            return false;
        }
    });


}

function renderList(data) {

    console.log(data);

    currentList = data;

    if (currentList.length > 0) {
        $('#dashboard-widgets').html("");
        $.each(currentList, function (index, widget) {
            // TODO: Render
            $('#dashboard-widgets').append('<div data-title="' + widget.title + '" id="' + widget.id + '" class="edit-widget" style="display: none;width: ' + widget.width + 'px; height:' + widget.height + 'px; background-color: ' + widget.color + ';float: left"></div>');

            $('#' + widget.id).show(2500);

        });
    }
}

function cleanForm() {
    $(".invalid-feedback").hide();
    $('#widget-id').val('');
    $('#title').val('');
    $('#color').val('');
    $('#width').val('');
    $('#height').val('');
}

function renderDetails(widget) {
    console.log(widget);
    $('#widget-id').val(widget.id);
    $('#title').val(widget.title);
    $('#color').val(widget.color);
    $('#width').val(widget.width);
    $('#height').val(widget.height);
}

function formToJSON() {
    return JSON.stringify({
        "id": $('#widget-id').val(),
        "title": $('#title').val(),
        "color": $('#color').val(),
        "width": $('#width').val(),
        "height": $('#height').val()
    });
}


function loginFormToJSON() {
    return JSON.stringify({
        "username": $('#username').val(),
        "password": $('#password').val()
    });
}

function validateForm() {

    var $validate = false;

    if ($('#title').val() == '') {
        $(".invalid-title").show();
        $validate = true;
    } else {
        $(".invalid-title").hide();
    }

    if ($('#color').val() == '') {
        $(".invalid-color").show();
        $validate = true;

    } else {
        $(".invalid-color").hide();
    }
    if ($('#width').val() == '') {
        $(".invalid-width").show();
        $validate = true;

    } else {
        $(".invalid-width").hide();
    }
    if ($('#height').val() == '') {
        $(".invalid-height").show();
        $validate = true;

    } else {
        $(".invalid-heigth").hide();
    }

    return $validate;
}

