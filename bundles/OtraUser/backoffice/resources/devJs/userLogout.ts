(async function(document : Document, window: Window, body: HTMLElement)
{
  'use strict';

  const
    CHUNKS_URL: number = 0,
    LOGIN_TITLE = 'Login',

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
          document.documentElement.dataset.page= 'login-page';
          body.classList.remove('suffix-col');
          body.classList.add('suffix-login');
          window.history.pushState(
            {},
            LOGIN_TITLE,
            window['JS_ROUTING'].login.chunks[CHUNKS_URL]
          );
          // Title is not implemented in most browsers as it is not standard, so we must set it explicitly
          document.title = LOGIN_TITLE;

          if (window['LoginForm'] === undefined)
          {
            for (let script of responseData.js)
            {
              let newScript = document.createElement('script');
              newScript.src = script.src;
              newScript.nonce = script.nonce;
              newScript.addEventListener('load', async () => (await window['LoginForm']).init());
              document.head.appendChild(newScript);
            }
          }
          else
            (await window['LoginForm']).addListeners();
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
