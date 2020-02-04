class Tiny
{
    commentEdit()
    {
        tinymce.init({
            selector: 'textarea#comment_Content',
            plugins: 'emoticons',
            toolbat: 'emoticons',
        });
    };
    subjectEdit()
    {
        tinymce.init({
            selector: 'textarea#subject_Content',
            plugins: 'emoticons',
            toolbat: 'emoticons',
        });
    };
}

let tiny = new Tiny();
tiny.commentEdit();
tiny.subjectEdit();