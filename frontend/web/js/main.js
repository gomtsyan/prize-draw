$( document ).ready(function()
{
    $('#start').click(function(e)
    {
        e.preventDefault();

        var token = $(this).attr('data-token');
        var start = true;

        axios
            .post('/site/start', 'start='+start, {
                headers: {'X-CSRF-Token' : token}
            })
            .then(function(res) {
                if(res.statusText === 'OK')
                {
                    var text = 'You Won ';
                    var result = res.data;
                    if(result.type === 'money'){
                        text += '$ '+result.present;
                    }else if(result.type === 'thing'){
                        text += result.present;
                    }else if(result.type === 'points'){
                        text += result.present+' points';
                    }
                    $('#startDiv').hide();
                    $('#'+result.type).show();
                    $('#'+result.type+' h2').text(text);

                    console.log(result);
                }
            })
            .catch(function(err)
            {
                console.log(err);
            });
    });

    $('.money').click(function(e)
    {
        var type = $(this).attr('data-type');
        if(type === 'getLater')
        {

        }elseif(type === 'convertToPoints')
        {

        }elseif(type === 'addToBank')
        {



        }elseif(type === 'refuseMoney')
        {

        }
    });

    $('.points').click(function(e)
    {
        var type = $(this).attr('data-type');
        if(type === 'topUpAccount')
        {

        }elseif(type === 'refusePoints')
        {

        }
    });

    $('.thing').click(function(e)
    {
        var type = $(this).attr('data-type');
        if(type === 'receiveByMail')
        {

        }elseif(type === 'refuseThing')
        {

        }
    });
});