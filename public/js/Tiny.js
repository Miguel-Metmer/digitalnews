class Tiny
{
    commentEdit()
    {
        tinymce.init({
            selector: 'textarea#comment_Content',
            plugins: 'emoticons',
            toolbat: 'emoticons',
            resize: false
        });
    };
    subjectEdit()
    {
        tinymce.init({
            selector: 'textarea#subject_Content',
            plugins: 'emoticons',
            toolbat: 'emoticons',
            resize: false
        });
    };
}

let tiny = new Tiny();
tiny.commentEdit();
tiny.subjectEdit();