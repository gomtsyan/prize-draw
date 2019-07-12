$( document ).ready(function()
{
    const bankAPI = 'http://localhost:8000/api/user';
    const bankLoginAPI = 'http://localhost:8000/api/users/login';
    const userEmail = $('#userEmail').attr('data-userEmail');
    const userId = $('#userId').attr('data-userId');
    const csrfToken = $('#csrfToken').attr('data-token');

    $('#start').click(function(e)
    {
        e.preventDefault();
        var start = true;

        axios
            .post('/site/start', 'start='+start, {
                headers: {'X-CSRF-Token' : csrfToken}
            })
            .then(function(res) {
                if(res.statusText === 'OK')
                {
                    if(res.data.status !== 'error')
                    {
                        var text = 'You Won ';
                        var result = res.data;
                        switch (result.type) {
                            case 'money':
                                text += '$ '+result.present;
                                break;
                            case 'thing':
                                text += result.present;
                                break;
                            case 'points':
                                text += result.present+' points';
                                break;
                        }

                        $('#startDiv').hide();
                        $('#'+result.type).show().attr('data-present', result.present);
                        $('#'+result.type+' h2').text(text);
                    }else{
                        $('#startDiv').html('<h2>Try again !</h2>');
                    }
                }
            })
            .catch(function(err)
            {
                console.log(err);
            });
    });

    $('.savePresent').click(function(e)
    {
        var type = $(this).attr('data-type');
        var presentType = $(this).attr('data-presentType');
        var present = $('#'+presentType).attr('data-present');
        var data = 'userId='+userId+'&present='+present+'&presentType='+presentType;
        if(type === 'convertToPoints') {
            data += '&type='+type
        }

        axios
        .put('/site/save-present',
            data,
            {
                headers: {'X-CSRF-Token' : csrfToken}
            }
        )
        .then(function(res) {
            console.log(res);
            if(res.statusText === 'OK')
            {
                if(!res.data.error)
                {
                    var result = res.data.message;

                    $('#'+presentType).html('<h2>'+result+'</h2>');

                }else{
                    $('#'+presentType).html('<h2>'+res.data.error+'</h2>');
                }
            }
        })
        .catch(function(err)
        {
            console.log(err);
        });
    });

    $('#addToBank').click(function(e)
    {
        var presentType = $(this).attr('data-presentType');
        var present = $('#'+presentType).attr('data-present');

        axios
            .post(bankLoginAPI,
                'email='+userEmail
            )
            .then(function(res) {

                if(res.statusText === 'OK')
                {
                    if(res.data.success)
                    {
                        axios
                            .put(bankAPI, {email:userEmail, money:present}, {
                                headers: {'x-access-token' : res.data.token}
                            })
                            .then(function(result) {
                                console.log(result);
                                if(result.statusText === 'OK')
                                {
                                    if(result.data.status === 'OK'){
                                        var text = '<h2>Bank account received</h2>';
                                        $('#money').html(text);
                                    }

                                }
                            })
                            .catch(function(err)
                            {
                                console.log(err);
                            });
                    }
                }
            })
            .catch(function(err)
            {
                console.log(err);
            });
    });

    $('.refuse').click(function(e)
    {
        location.reload();
    });

});