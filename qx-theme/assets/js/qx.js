/**
 * QX Media — the entire client-side runtime.
 *
 * Three jobs: the nav's transparent-to-solid state, the mobile drawer, and a scroll-reveal
 * fallback for browsers without CSS scroll-driven animations. Everything else is CSS.
 *
 * The reveal is deliberately CSS-first: where `animation-timeline: view()` is supported
 * (Chromium, and Safari 26+) there is no JavaScript involved at all and no scroll listener,
 * so it cannot cost INP. Only Firefox takes the observer path.
 *
 * No dependencies, no build step, ~1.5 KB.
 */
(function () {
  'use strict'

  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches

  /* ---- Nav: solid after 40px, per the handoff -------------------------
     An IntersectionObserver on a sentinel rather than a scroll listener, so there
     is no per-frame main-thread work. */
  var nav = document.querySelector('[data-qx-nav]')

  if (nav && 'IntersectionObserver' in window) {
    var sentinel = document.createElement('div')
    sentinel.setAttribute('aria-hidden', 'true')
    sentinel.style.cssText = 'position:absolute;top:40px;height:1px;width:1px;pointer-events:none;'
    document.body.prepend(sentinel)

    new IntersectionObserver(
      function (entries) {
        nav.classList.toggle('is-solid', !entries[0].isIntersecting)
      },
      { threshold: 0 }
    ).observe(sentinel)
  }

  /* ---- Nav over the 3D hero -------------------------------------------
     The homepage hero is near-black and five screens tall, so the nav needs cream
     type and a cream mark for as long as the hero is behind it, then hands back to
     the normal cream bar. Driven off the hero's own geometry rather than a scroll
     threshold, so it stays correct at any pin length and after a resize.

     A second IntersectionObserver would only fire at the boundaries; the nav needs
     to flip at the exact pixel the hero's bottom edge clears the 80px bar, so this
     reads the rect — but only inside a rAF that is scheduled by a passive scroll
     listener, never per scroll event. */
  var hero = document.querySelector('[data-qx-hero]')

  if (nav && hero) {
    var navH = 80
    var queued = false

    var syncDark = function () {
      queued = false
      var r = hero.getBoundingClientRect()
      nav.classList.toggle('qx-nav--dark', r.top <= navH && r.bottom > navH)
    }

    var onScrollDark = function () {
      if (queued) return
      queued = true
      requestAnimationFrame(syncDark)
    }

    window.addEventListener('scroll', onScrollDark, { passive: true })
    window.addEventListener('resize', onScrollDark, { passive: true })
    syncDark()
  }

  /* ---- Scroll reveal fallback ----------------------------------------- */
  var supportsViewTimeline =
    window.CSS && CSS.supports && CSS.supports('animation-timeline', 'view()')

  if (!supportsViewTimeline && !reduce) {
    document.documentElement.classList.add('qx-js-reveal')
    var items = document.querySelectorAll('.qx-reveal')

    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (e) {
            if (!e.isIntersecting) return
            e.target.classList.add('is-in')
            io.unobserve(e.target)
          })
        },
        { rootMargin: '0px 0px -10% 0px' }
      )
      items.forEach(function (el) {
        io.observe(el)
      })
    } else {
      // No observer at all: show everything rather than leave text invisible.
      document.documentElement.classList.remove('qx-js-reveal')
    }
  }

  /* ---- Full-screen mobile menu panel ---------------------------------- */
  var burger = document.querySelector('[data-qx-burger]')
  var panel = document.querySelector('[data-qx-panel]')

  if (burger && panel) {
    var setOpen = function (open) {
      burger.setAttribute('aria-expanded', open ? 'true' : 'false')
      panel.hidden = !open
      document.documentElement.style.overflow = open ? 'hidden' : ''

      if (open) {
        var close = panel.querySelector('[data-qx-panel-close]')
        if (close) close.focus()
      } else {
        burger.focus()
      }
    }

    burger.addEventListener('click', function () {
      setOpen(burger.getAttribute('aria-expanded') !== 'true')
    })

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !panel.hidden) setOpen(false)
    })

    // The × closes it; so does following any link, so the panel is never left
    // covering the page it just navigated to.
    panel.addEventListener('click', function (e) {
      if (e.target.closest('[data-qx-panel-close], a')) setOpen(false)
    })

    // If the viewport grows past the breakpoint while open, CSS hides the panel
    // but it would stay in the accessibility tree.
    window.matchMedia('(min-width: 921px)').addEventListener('change', function (e) {
      if (e.matches && !panel.hidden) setOpen(false)
    })
  }

  /* ---- Contact form -> WhatsApp ---------------------------------------
     The handoff specifies the form opens wa.me with a pre-filled message and no
     backend. Progressive enhancement: without JavaScript the form still submits
     to a mailto: action, so a lead is never silently lost. */
  var form = document.querySelector('[data-qx-waform]')

  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault()
      var data = new FormData(form)
      var lines = []
      data.forEach(function (value, key) {
        if (String(value).trim() !== '') lines.push(key + ': ' + value)
      })
      var phone = form.getAttribute('data-phone') || ''
      var url = 'https://wa.me/' + phone + '?text=' + encodeURIComponent(lines.join('\n'))
      window.open(url, '_blank', 'noopener')
    })
  }
})()
