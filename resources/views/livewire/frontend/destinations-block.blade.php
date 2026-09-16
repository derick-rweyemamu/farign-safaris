<div class="dest-grid">
  @foreach($destinations as $destination)
  <a href="{{ url('destinations/' . $destination->slug) }}" class="dest-card">
    <img src="{{ asset('storage/' . $destination->featured_image) }}" alt="{{ $destination->name }}">
    <div class="dest-overlay">
      <div class="dest-name">{{ $destination->name }}</div>
      <div class="dest-sub">{{ $destination->tagline ?? 'Discover ' . $destination->name }}</div>
    </div>
  </a>
  @endforeach
</div>
