<div class="mb-3">
    <label for="title" class="form-label">Title</label>
    <input type="text" name="title" id="title" maxlength="255"
           class="form-control @error('title') is-invalid @enderror"
           value="{{ old('title', $post->title ?? '') }}">
</div>
<div class="mb-3">
    <label for="content" class="form-label">Content</label>
    <textarea name="content" id="content" rows="6"
              class="form-control @error('content') is-invalid @enderror">{{ old('content', $post->content ?? '') }}</textarea>
</div>
