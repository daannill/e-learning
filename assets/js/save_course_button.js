const saveButtons = document.querySelectorAll('.course-save');

saveButtons.forEach(button => {

    button.addEventListener('click', async () => {

        const courseId = button.dataset.courseId;

        const csrfToken = button.dataset.csrfToken;

        button.classList.toggle('active');

        button.disabled = true;

        try {

            const formData = new FormData();

            formData.append('_token', csrfToken);
            formData.append('course_id', courseId);

            const response = await fetch(
                '/elearning/api/course/save',
                {
                    method: 'POST',
                    body: formData
                }
            );

            if (!response.ok) {
                throw new Error('Request failed');
            }

            const result = await response.json();

            button.classList.toggle(
                'active',
                result.saved
            );

        } catch (error) {

            console.error(error);

            button.classList.toggle('active');

            alert(
                'Failed to save course. Please try again.'
            );

        } finally {

            button.disabled = false;

        }

    });

});