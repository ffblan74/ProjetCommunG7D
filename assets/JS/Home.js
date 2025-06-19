// Animation des compteurs
document.addEventListener('DOMContentLoaded', function() {
  const counters = document.querySelectorAll('.count');
  const speed = 200;
  
  const animateCounters = () => {
    counters.forEach(counter => {
      const target = +counter.parentElement.getAttribute('data-target');
      const count = +counter.innerText;
      const increment = target / speed;
      
      if (count < target) {
        counter.innerText = Math.ceil(count + increment);
        setTimeout(animateCounters, 1);
      } else {
        counter.innerText = target;
      }
    });
  };
  
  // Déclencher l'animation lorsque la section est visible
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        animateCounters();
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.5 });
  
  document.querySelectorAll('.stat-card').forEach(card => {
    observer.observe(card);
  });
});