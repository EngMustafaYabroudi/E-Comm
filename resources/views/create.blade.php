<!DOCTYPE html>

<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.2/css/bulma.min.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>

  </head>

  <body>
    <section class="section">
      <div class="container">
        <div class="title is-2">Create New product</div>
        <form action="{{ route('products.store') }}" method="POST"enctype="multipart/form-data">
          @csrf
              <div class="field">
                <label class="label">product Name</label>
                <div class="control">
                  <input class="input @error('name')is-danger @enderror" name="name" type="text" value="{{ old('name') }}" placeholder="name">
                </div>
                @error('name')
                  <p class="help is-danger">{{ $message }}</p>
                @enderror
              </div>

              <div class="field">
                <label class="label">exprite date</label>
                <div class="control">
                  <input class="input @error('expiry_date')is-danger @enderror" name="expiry_date" type="date" value="{{ old('expiry_date') }}" placeholder="expiry_date">
                </div>
                @error('expiry_date')
                  <p class="help is-danger">{{ $message }}</p>
                @enderror
              </div>
              <div class="field">
                <label class="label">Category</label>

                <div class="control">
                  <div class="select @error('category_id')is-danger @enderror">
                    <select name="category_id" value="{{ old('category_id') }}">
                      @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                @error('category_id')
                  <p class="help is-danger">{{ $message }}</p>
                @enderror
              </div>

              <div class="field">
                <label class="label"> information per</label>
                <div class="control">
                  <input class="input @error('commun_info')is-danger @enderror" name="commun_info" type="text" value="{{ old('commun_info') }}" placeholder="http://hi.com/pic.jpg">
                </div>
                @error('commun_info')
                  <p class="help is-danger">{{ $message }}</p>
                @enderror
              </div>
              <div class="field">
                <label class="label"> Image (upload)</label>
                <div class="file">
                  <label class="file-label">
                    <input class="file-input" type="file" id="image" name="image" accept="product/*">
                    <span class="file-cta">
                      <span class="file-icon">
                        <i class="fas fa-upload"></i>
                      </span>
                      <span class="file-label">
                        Choose an image…
                      </span>
                    </span>
                  </label>
                </div>
                @error('image')
                  <p class="help is-danger">{{ $message }}</p>
                @enderror
              </div>

              <div class="field">
                <label class="label"></label>
                <div class="control">
                  <input name="quantity" class="input @error('quantity')is-danger @enderror"  type="number">
                @error('quantity')
                    <p class='help is-danger'>{{$message}}</p>
                @enderror

                </div>
                @error('quantity')
                  <p class="help is-danger">{{ $message }}</p>
                @enderror
              </div>
              <div class="field">
                <label class="label"></label>
                <div class="control">
                  <input name="regular_price" class="input @error('regular_price')is-danger @enderror"  type="number">
                @error('regular_price')
                    <p class='help is-danger'>{{$message}}</p>
                @enderror

                </div>
                @error('regular_price')
                  <p class="help is-danger">{{ $message }}</p>
                @enderror
              </div>

          <div class="field is-grouped">
            <div class="control">
              <button class="button is-link">Create new product</button>
            </div>
            <div class="control">
              <button class="button is-link is-light">Cancel</button>
            </div>
          </div>
        </form>
      </div>
    </section>
  </body>
</html>


