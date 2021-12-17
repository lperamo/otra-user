(async function(document : Document, window: Window, body: HTMLElement)
{
  'use strict';

  let
    $form : HTMLFormElement,
    $errorMessage : HTMLDivElement;

  const
    CHUNKS_URL: number = 0,
    simpleHeaders: any = {
      'x-requested-with': 'xmlhttprequest'
    },
    basicPostInit:RequestInit = {
      method : 'POST',
      credentials : 'same-origin'
    },
    loginTitle = 'Login',
    usersManagementTitle = 'Users management',
    logIn = async function logIn(mouseEvent : MouseEvent) : Promise<false>
    {
      mouseEvent.preventDefault();

      if (!this.reportValidity())
        return false;

      const
        formData: FormData = new FormData(this);

      formData.set('otra-remember', (formData.get('otra-remember') === 'on') ? '1' : '0');

      const
        response : Response = await fetch(
          window['JS_ROUTING'].loginCheck.chunks[CHUNKS_URL],
      {
            ...basicPostInit,
            ...{
              body : formData,
              headers: simpleHeaders
            }
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
          // Removes the error message if it is there
          $errorMessage.classList.toggle('error-message--hidden', true);

          body.innerHTML = responseData.html;
          body.classList.remove('login-page');
          body.classList.add('users-page');
          window.history.pushState(
            {
              requestInfo : window['JS_ROUTING'].users.chunks[CHUNKS_URL],
              requestInit : {
                ...basicPostInit,
                headers: simpleHeaders
              },
              title: usersManagementTitle
            },
            usersManagementTitle,
            window['JS_ROUTING'].users.chunks[CHUNKS_URL]
          );

          // Title is not implemented in most browsers as it is not standard, so we must set it explicitly
          document.title = usersManagementTitle;
        } else
        {
          // Adds the error message if it is not already there
          $errorMessage.classList.toggle('error-message--hidden', false);
          // hides the waiting message
          // shows the waiting message
        }
      }

      // message d'attente à prévoir

      return false;
    },

    onPopState = async function onPopState(event: PopStateEvent): Promise<false>
    {
      const response : Response = await fetch(
          event.state.requestInfo,
          event.state.requestInit
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
          body.classList.toggle('users-page', true);
        } else
        {
          // hides the waiting message
          // shows the waiting message
        }
      }

      return false;
    },

    pageReady = async function pageReady() : Promise<void>
    {
      window.history.replaceState(
  {
          requestInfo : window['JS_ROUTING'].login.chunks[CHUNKS_URL],
          requestInit : {
            ...basicPostInit,
            headers: simpleHeaders
          },
          title : loginTitle
        },
        loginTitle,
        window['JS_ROUTING'].login.chunks[CHUNKS_URL]
      );
      $errorMessage = document.querySelector('.error-message');
      $form = <HTMLFormElement> document.getElementsByClassName('login-box')[0];
      $form.addEventListener('submit', logIn);
      window.addEventListener('popstate', onPopState);
    };

  'loading' !== document.readyState
    ? await pageReady()
    : document.addEventListener('DOMContentLoaded', pageReady);
})(document, window, document.body);
