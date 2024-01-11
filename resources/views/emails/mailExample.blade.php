<body class="antialiased">
    <div
      class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
    
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        @if (session('status'))
        <div class="alert alert-success">
          {{ session('status') }}
        </div>
        @endif
        <div class="flex justify-center pt-8 sm:justify-start sm:pt-0">
          <h2>Bacancy Technology Mail Sending Tutorials</h2>
        </div>
    
        <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
          <div class="grid grid-cols-1 md:grid-cols-2">
            <div class="p-6">
              
    
              <div class="ml-12">
                <form action="{{route('send.email')}}" method="POST">
                  @csrf
                  <h6>Enter Name</h6>
                  <input style="background:DarkGrey; width:500px; height:35px" type="text" name="name" value="" />
                  <br>
                  <h6>Enter Email </h6>
                  <input style="background:DarkGrey; width:500px; height:35px" type="email" name="email" id="email">
                  <br><br><br>
                  <input class="btn btn-dark btn-block" type="submit" value="submit" name="submit">
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
   </body>