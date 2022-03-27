(async function (document: Document, body : HTMLElement) : Promise<void>
{
  'use strict';

  const
    login = async function() : Promise<void>
    {
      document.documentElement.dataset.page = 'users-page';
      body.classList.remove('align-items--center', 'justify-content--center');
    },
    pageReady = async function pageReady() : Promise<void>
    {
      (await window['LoginForm']).init();
      body.addEventListener('loginEvent', login);
    };

  'loading' !== document.readyState
    ? await pageReady()
    : document.addEventListener('DOMContentLoaded', pageReady);
})(document, document.body);
