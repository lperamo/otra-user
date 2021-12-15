(async function(document : Document, window: Window, body: HTMLElement)
{
  'use strict';

  const CHUNKS_URL: number = 0,

    logout = async function logout(): Promise<void>
    {
      const
        myHeaders = new Headers({
          'x-requested-with': 'xmlhttprequest'
        }),
        response : Response = await fetch(
          window['JS_ROUTING'].logout.chunks[CHUNKS_URL],
          {
            method : 'POST',
            credentials : 'same-origin',
            headers: myHeaders
          }
        ),
        responseData = await response.json();

      if (response.ok === false)
      {
        // hides the waiting message
        // shows the waiting message
      } else
      {
        if (responseData.success === true)
        {
          body.innerHTML = responseData.html;
          body.classList.remove('users-page');
          body.classList.add('login-page');
          window.history.pushState(
            {},
            'Login',
            window['JS_ROUTING'].login.chunks[CHUNKS_URL]
          );
        } else
        {
          // hides the waiting message
          // shows the waiting message
        }
      }
    },

    init = async function pageReady() : Promise<void>
    {
      document.querySelector('.-logout').addEventListener('mouseup', logout);
    };

  'loading' !== document.readyState
    ? await init()
    : document.addEventListener('DOMContentLoaded', init);
})(document, window, document.body);
