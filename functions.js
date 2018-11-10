$(document).ready(function () {
    // Підключення стилів випадаючого меню
    PNotify.prototype.options.styling = "bootstrap3";
    PNotify.prototype.options.styling = "fontawesome";

    var action_buttons =
        '<button class="btn btn-info btn-sm dropdown-toggle" type="button" id="postStatusSelector" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
        '    Cтатус' +
        '  </button>' +
        '  <div class="dropdown-menu" aria-labelledby="postStatusSelector" id="postStatusSelectorOptions">' +
        '    <a class="dropdown-item" id="solvedButton" value="publish">Опубліковано</a>' +
        '    <a class="dropdown-item" id="notsolvedButton" value="draft">Приховано</a>' +
        '  </div>' +
        '<button type="button" class="btn btn-sm btn-danger" id="deleteAuthorsPostButton">Видалити</button>';


    /* Викорисовуючи datatables створюємо табличку авторів і присвоюємо індекси полям*/
    var table_author = $('#authors-table').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Ukrainian.json"
        },
        ajax: {
            method: "POST",
            url: ajaxurl,
            data: {action: 'get_authors_data'},
            dataSrc: ""
        },
        columns: [
            {title: 'Автор', data: 'author_url'},
            {title: 'Ім\'я', data: 'author_name'},
            {title: 'Телефон', data: 'author_phone'},
            {title: 'К-сть всіх постів', data: 'count_all_posts'},
            {title: 'К-сть опублікованих', data: 'count_posts'}
        ]
    });

    /* Викорисовуючи datatables створюємо табличку з постами автора і присвоюємо індекси полям*/
    var table_post = $('#authors-posts-table').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Ukrainian.json"
        },
        ajax: {
            method: "POST",
            url: ajaxurl,
            data: {action: 'get_authors_posts_data', customerID: customerID},
            dataSrc: ""
        },
        columns: [
            {title: 'Статус', data: 'post_status_badge'},
            {title: 'Код товару', data: 'post_id'},
            {title: 'Назва товару', data: 'post_url'},
            {title: 'Дата', data: 'date'},
            {title: 'Операції', defaultContent: action_buttons, orderable: false, width: "200px" }
        ]
    });

    /* Обробка події при натиску кнопки видалення оголошення*/
 $('#authors-posts-table tbody').on('click', '#deleteAuthorsPostButton',  function () {
        var data = table_post.row($(this).parents('tr')).data();
         $.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: "delete_authors_post",
                post_id: data.post_id
            }
        })
            .done(function () {
                   console.log("delete done");
                   table_post.ajax.reload();

                new PNotify({
                    title: 'Виконано!',
                    text: 'Запис успішно видалено',
                    type: 'success'
                });
            })
            .fail(function () {
                new PNotify({
                    title: 'Помилка!',
                    text: 'Виникла помилка в процесі обробки запиту',
                    type: 'error'
                });
            })
    });

    /* Обробка події при натиску кнопки зміни статусу оголошення*/
$('#authors-posts-table tbody').on('click', '#postStatusSelectorOptions a', function () {
        console.log(this);
        var new_post_status_code = $(this).attr("value");
        var data = table_post.row($(this).parents('tr')).data();

       $.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: "update_post_status_code",
                post_id: data.post_id,
                new_post_status_code: new_post_status_code
            }
        })
            .done(function () {
                console.log("update done");
                table_post.ajax.reload();
                new PNotify({
                    title: 'Виконано!',
                    text: 'Видимість оголошення оновлено',
                    type: 'success'
                });
            })
            .fail(function () {
                new PNotify({
                    title: 'Помилка!',
                    text: 'Виникла помилка в процесі обробки запиту',
                    type: 'error'
                });
            })
    });


});