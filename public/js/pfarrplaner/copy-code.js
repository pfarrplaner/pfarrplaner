$(document).ready(function(){
    $('#code').click(function(){ $('#msgCopied').html(''); });
    $('#code').change(function(){ $('#msgCopied').html(''); });
    $('#code').blur(function(){ $('#msgCopied').html(''); });
    $('#code').on('keyup', function(){ $('#msgCopied').html(''); });
    $('#btnCopy').on('click', function(e){
        e.preventDefault();
        $('#code').focus();
        $('#code').select();
        document.execCommand('copy');
        $('#msgCopied').html('Der Code wurde in die Zwischenablage kopiert.');
    });

});
