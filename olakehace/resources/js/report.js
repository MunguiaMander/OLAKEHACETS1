document.addEventListener('DOMContentLoaded', function () {
    var reportModal = document.getElementById('reportModal');
    if (reportModal) {
        $('#reportModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var postId = button.data('post-id');
            console.log("Post ID capturado:", postId); 
            var modal = $(this);
            modal.find('#post_id').val(postId);  
        });
    }
});