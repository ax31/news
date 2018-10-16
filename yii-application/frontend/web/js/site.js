$(function () {
    $('body').on('click', '.show-more', function () {
        const news = $(this).closest('.panel');

        $(this).hide();
        news.find('.truncate-content').hide();
        news.find('.content').removeClass('hidden');
    });

    $('body').on('click', '.like', function () {
        const thisLike = $(this);
        const newsId = thisLike.closest('.panel').data('id');

        $.post('index.php?r=news/like', {
            'id': newsId
        }, function (data) {
            if (data === 'insert') {
                thisLike.removeClass('btn-default').addClass('btn-primary');
            } else if (data === 'delete') {
                thisLike.removeClass('btn-primary').addClass('btn-default');
            }
        }, 'json');
    });
});