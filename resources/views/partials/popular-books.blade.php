<div style="background: #cae28c;
    border: 2px solid #5a8c69; border-radius: 16px;
    padding: 14px;
    display: flex;
    flex-direction: column;
    height: 220px ;
    width: 620px;
    box-sizing: border-box;">
    <h3 style="font-family: 'Henny Penny', cursive; color: #1a3c11; font-size: 15px; margin: 0 0 14px 0; font-weight: normal;">
        Popular books this week:
    </h3>

    <div style="display: flex; flex-direction: row; align-items: flex-start; justify-content: space-between; gap: 10px; overflow-x: auto; padding-bottom: 6px;">
        @forelse($popularBooks as $pBook)
            <div onclick="window.location.href='/books/{{ $pBook['id'] }}'" 
                 style="display: flex; flex-direction: column; align-items: center; width: 95px; min-width: 68px; text-align: center; cursor: pointer; transition: transform 0.15s ease;"
                 onmouseenter="this.style.transform='translateY(-3px)'"
                 onmouseleave="this.style.transform='translateY(0)'">
                
                <img src="{{ $pBook['cover'] }}" 
                     alt="{{ $pBook['title'] }}" 
                     loading="lazy"
                     referrerpolicy="no-referrer"
                     onerror="this.onerror=null; this.src='https://via.placeholder.com/120x180?text=No+Cover';"
                     style="width: 70px; height: 110px; object-fit: cover; border-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.15); background: #e8f0dc;">

                <div style="font-family: 'Unkempt', cursive; font-size: 11px; font-weight: bold; color: #1f5117; width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 6px;" title="{{ $pBook['title'] }}">
                    {{ $pBook['title'] }}
                </div>

                <div style="font-family: 'Unkempt', cursive; font-size: 10px; color: #666; width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $pBook['author'] }}">
                    {{ $pBook['author'] }}
                </div>
            </div>
        @empty
            <div style="font-family: 'Unkempt', cursive; font-size: 12px; color: #777; width: 100%; text-align: center;">
                Couldn't load the popular books.
            </div>
        @endforelse
    </div>
</div>