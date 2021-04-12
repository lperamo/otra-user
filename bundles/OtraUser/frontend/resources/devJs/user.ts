(function(document : Document, window: Window, undef: undefined)
{
  'use strict';

  let $form : HTMLFormElement;
  const route : string = '/login-check';

  /**
   * Checks any server error, warning ...whatever.
   *
   * @param {Response} response
   *
   * @returns Promise<string>
   */
  let checkStatus = function checkStatus(response: Response) : Promise<string>
  {
    if (true !== response.ok)
    {
      console.log('Looks like there was a problem. Status Code: ' + response.status);
      return;
    }

    return response.text();
  };

  let
    loginReturn = function loginReturn()
    {
      console.log('loginReturn');
    },

    loginError = function loginError()
    {
      console.log('loginError');
    },

    logIn = function logIn(event : MouseEvent) : boolean
    {
      console.log(event.target);
      let myPromise : Promise<string | Response> = window.fetch(
        route,
        {
          body : new FormData($form),
          method : 'POST',
          credentials : 'same-origin',
          headers : new Headers({
            Accept: 'text/plain',
            'Content-Type': 'application/x-www-form-urlencoded'
          })
        }
      );

      myPromise = myPromise.then(checkStatus);
      myPromise.then(loginReturn);

      if (undef !== loginError)
        myPromise.then(loginError);
      else
        console.log('error'); // TODO implement it !

      return false;
    },

    pageReady = function pageReady() : void
    {
      console.log('test');
      $form = <HTMLFormElement> document.getElementsByClassName('login-box')[0];
      $form.addEventListener('submit', logIn);
    }

  'loading' !== document.readyState
    ? pageReady()
    : document.addEventListener('DOMContentLoaded', pageReady);
})(document, window, undefined);
