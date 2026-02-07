                        {{-- <button @click="open = !open" @click.outside="open = false"
                                class="text-gray-400 hover:text-gray-600">
                            ⋮
                        </button>

                        <div x-show="open" x-transition
                             class="absolute left-0 mt-2 w-40 bg-white rounded-md shadow-lg z-50 border py-1"
                             style="display:none;">

                            <a href="{{ route('students.show',$student->id) }}"
                               class="block px-4 py-2 text-sm hover:bg-gray-50">عرض</a>

                            <a href="{{ route('students.edit',$student->id) }}"
                               class="block px-4 py-2 text-sm hover:bg-gray-50">تعديل</a>

                            <form method="POST" action="{{ route('students.destroy',$student->id) }}">
                                @csrf
                                @method('DELETE')
                                <button class="w-full text-right px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    حذف
                                </button>
                            </form>
                        </div> --}}

                        
                            {{-- button delete --}}
                            {{-- <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg> --}}