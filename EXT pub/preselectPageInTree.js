
// TEST FEATURE


console.log('injected: preselectPageInTree.js');

var PID = '12';
var regExpPage = new RegExp( '/id='+PID+'+/', 'g' );



// easiest way to check if the page tree is loaded yet
var treeLoaded = setInterval(function () {
    if ( !document.querySelector('#typo3-pagetree-tree .t3js-icon') )
        return;
    clearInterval(treeLoaded);

    console.log('#typo3-pagetree-tree READY!');

    var pages = document.querySelectorAll('#typo3-pagetree-tree .t3js-icon');

    console.log( pages );
    console.log( document.querySelectorAll('[data-title]') );

    for(var i = 0; i < pages.length; i++){
        var node = pages[i];
        var title = node.getAttribute('data-title');

        console.log(title);

        if ( typeof title === 'string' )    {

            var currentPageUid = (typeof title === 'string'
                ?  title.match(/id=([0-9])+/g, '').toString().replace('id=', '')   // it matches whole string. how to get only id with regexp in js?
                :  '0');

            console.log(currentPageUid);

            // todo: both to integer!
            if (currentPageUid === PID) {
                console.log( 'FOUND!' );

                node.parentNode.click();
            }

            // todo: open Page module!

        }
    }

    // todo: stop loop if not found after some time
}, 30);
