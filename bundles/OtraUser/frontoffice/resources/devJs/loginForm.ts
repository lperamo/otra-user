(async function(document : Document, window: Window, body: HTMLElement)
{
  'use strict';

  let
    $form : HTMLFormElement,
    $errorMessage : HTMLDivElement;
  const
    CHUNKS_URL: number = 0,
    logIn = async function logIn(mouseEvent : MouseEvent) : Promise<false>
    {
      mouseEvent.preventDefault();

      if (!this.reportValidity())
        return false;

      const
        formData: FormData = new FormData(this);

      formData.set('otra-remember', (formData.get('otra-remember') === 'on') ? '1' : '0');

      const
        myHeaders = new Headers({
          'x-requested-with': 'xmlhttprequest'
        }),
        response : Response = await fetch(
          window['JS_ROUTING'].loginCheck.chunks[CHUNKS_URL],
          {
            body : formData,
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
          // Removes the error message if it is there
          $errorMessage.classList.toggle('error-message--hidden', true);

          body.innerHTML = responseData.html;
          body.classList.remove('login-page');
          body.classList.add('users-page');
          window.history.pushState(
            {},
            'Users management',
            window['JS_ROUTING'].users.chunks[CHUNKS_URL]
          );
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

    pageReady = async function pageReady() : Promise<void>
    {
      $errorMessage = document.querySelector('.error-message');
      $form = <HTMLFormElement> document.getElementsByClassName('login-box')[0];
      $form.addEventListener('submit', logIn);
    };

  'loading' !== document.readyState
    ? await pageReady()
    : document.addEventListener('DOMContentLoaded', pageReady);
})(document, window, document.body);
