interface IScript {
  src: string,
  nonce: string,
}

interface IStyleSheet {
  href: string,
  media: string,
  nonce: string,
}

interface ICmsResponse {
  css?: IStyleSheet[],
  html: string,
  js?: IScript[],
  success: boolean
}

window['spaCall'] = (async (document: Document, html: HTMLElement, undef: undefined)
  : Promise<{
  $ajaxRightSide: HTMLDivElement,
  spaCall: (
    target: HTMLElement,
    initData: RequestInit,
    changingPage?: boolean,
    $selector?: HTMLElement,
    responseFailureCallback?: (Promise<any> | null),
    successCallback?: (Promise<any> | null),
    failureCallback?: (Promise<any> | null)
  ) => Promise<void>
}> =>
{
  'use strict';
  const
    loadedJs : string[] = [],
    loadedCss : string[] = [];

  let
    $ajaxRightSide : HTMLDivElement;

  const
    CHUNKS_URL: number = 0,
    simpleHeaders: any = {
      'x-requested-with': 'xmlhttprequest'
    },
    /**
     * Fetches a page and updates the content of the backoffice right div
     *
     * @param {HTMLElement}   target                  Button used to launch the AJAX call
     * @param {RequestInit}   initData
     * @param {boolean}       changingPage            Do we stay on the same page?
     * @param {HTMLElement}   $selector
     * @param {?Promise<any>} responseFailureCallback
     * @param {?Promise<any>} successCallback
     * @param {?Promise<any>} failureCallback
     */
    spaCall = async function spaCall(
      target: HTMLElement,
      initData: RequestInit,
      changingPage: boolean = true,
      $selector : HTMLElement = $ajaxRightSide,
      responseFailureCallback: Promise<any>|null = null,
      successCallback: Promise<any>|null = null,
      failureCallback: Promise<any>|null = null
    ) : Promise<void>
    {
      const
        routeParameters: string = (target.dataset.routeParameters === undef ? '' : target.dataset.routeParameters),
        response : Response = await fetch(
          window['JS_ROUTING'][target.dataset.route].chunks[CHUNKS_URL] + routeParameters,
          {
            ...initData,
            ...{
              headers: simpleHeaders
            }
          }
        ),
        responseData:ICmsResponse = await response.json();

      if (response.ok === false)
      {
        // hides the waiting message
        // shows the waiting message
        if (responseFailureCallback !== null)
          (await responseFailureCallback)();
      } else
      {
        if (responseData.success)
        {
          // Removes the error message if it is there
          if (changingPage)
          {
            window.history.pushState(
              {
                requestInfo : window['JS_ROUTING'][target.dataset.route].chunks[CHUNKS_URL] + routeParameters,
                requestInit : {
                  ...initData,
                  headers: simpleHeaders
                },
                title: target.innerHTML
              },
              target.dataset.pageTitle,
              window['JS_ROUTING'][target.dataset.route].chunks[CHUNKS_URL] + routeParameters
            );

            html.dataset.page = target.dataset.class;
            $selector.innerHTML = responseData.html;

            // Title is not implemented in most browsers as it is not standard, so we must set it explicitly
            document.title = target.dataset.pageTitle;

            // We know this upstream, can we remove this condition?
            if (responseData.css !== undef)
            {
              for (const styleSheet of responseData.css)
              {
                if (loadedCss.indexOf(styleSheet.href) === -1)
                {
                  const newStyleSheet = document.createElement('link');
                  newStyleSheet.href = styleSheet.href;

                  if (styleSheet.media !== 'screen')
                    newStyleSheet.media = styleSheet.media;

                  newStyleSheet.nonce = styleSheet.nonce;
                  newStyleSheet.rel = 'stylesheet';
                  document.head.appendChild(newStyleSheet);
                  loadedCss.push(styleSheet.href);
                }
              }
            }

            // We know this upstream, can we remove this condition?
            if (responseData.js !== undef)
            {
              for (const script of responseData.js)
              {
                if (loadedJs.indexOf(script.src) === -1)
                {
                  const newScript = document.createElement('script');
                  newScript.src = script.src;
                  newScript.nonce = script.nonce;
                  document.head.appendChild(newScript);
                  loadedJs.push(script.src);
                }
              }
            }
          }

          if (successCallback !== null)
            (await successCallback)();
        } else
        {
          // hides the waiting message
          // shows the waiting message
          if (failureCallback !== null)
            (await failureCallback)();
        }
      }
    },
    pageReady = async function pageReady() : Promise<void>
    {
      $ajaxRightSide = <HTMLDivElement> document.getElementById('ajax--right-side');
    };

  'loading' !== document.readyState
    ? await pageReady()
    : document.addEventListener('DOMContentLoaded', pageReady);

  return {$ajaxRightSide, spaCall};
})(document, document.documentElement, undefined);
