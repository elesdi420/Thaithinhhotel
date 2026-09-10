/**
 * CMS Hotel - Exely Interactive UX Behaviors
 * Includes: Automatic Night Calculation, Mobile Sticky Booking Bar, and Smooth Micro-interactions
 */

(function($) {
  'use strict';

  $(document).ready(function() {
    initNightCounter();
    initMobileStickyBar();
    enhanceRoomCards();
  });

  /**
   * 1. Tự động tính số đêm lưu trú và gắn huy hiệu ("2 đêm", "3 đêm")
   */
  function initNightCounter() {
    function updateNightCount() {
      var dateFromVal = $('#check_in_time').val() || $('#daterange_value_from').text().trim();
      var dateToVal = $('#check_out_time').val() || $('#daterange_value_to').text().trim();

      if (dateFromVal && dateToVal) {
        var d1 = parseDate(dateFromVal);
        var d2 = parseDate(dateToVal);

        if (d1 && d2 && d2 > d1) {
          var diffTime = Math.abs(d2 - d1);
          var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

          $('.exely-night-badge').remove();
          var badgeHtml = '<span class="exely-night-badge">' + diffDays + ' đêm</span>';
          
          if ($('#daterange_value').length) {
            $('#daterange_value').append(badgeHtml);
          } else if ($('#daterange_value_to').length) {
            $('#daterange_value_to').append(badgeHtml);
          }
        }
      }
    }

    function parseDate(str) {
      if (!str) return null;
      var clean = str.replace(/[^\d\-\/]/g, '').trim();
      var parts = clean.split(/[\-\/]/);
      if (parts.length === 3) {
        // Hỗ trợ Y-m-d hoặc d-m-Y
        if (parts[0].length === 4) {
          return new Date(parts[0], parts[1] - 1, parts[2]);
        } else if (parts[2].length === 4) {
          return new Date(parts[2], parts[1] - 1, parts[0]);
        }
      }
      return null;
    }

    // Lắng nghe sự kiện thay đổi ngày
    $(document).on('change', '#check_in_time, #check_out_time', updateNightCount);
    setTimeout(updateNightCount, 800);
  }

  /**
   * 2. Thanh đặt phòng dính đáy màn hình trên thiết bị di động (Mobile Sticky Bar)
   */
  function initMobileStickyBar() {
    if ($('.room_cont').length === 0) return;

    var hotelName = $('#id_hotel_button option:selected').text().trim() || 'CMS Hotel';
    var dateFrom = $('#check_in_time').val() || 'Hôm nay';
    var dateTo = $('#check_out_time').val() || 'Ngày mai';

    var stickyHtml = [
      '<div class="exely-mobile-sticky-bar hidden-md hidden-lg">',
      '  <div class="exely-mobile-sticky-info">',
      '    <span class="exely-mobile-sticky-title">' + hotelName + '</span>',
      '    <span class="exely-mobile-sticky-dates">' + dateFrom + ' → ' + dateTo + '</span>',
      '  </div>',
      '  <button type="button" class="exely-mobile-sticky-btn">Xem phòng</button>',
      '</div>'
    ].join('');

    $('body').append(stickyHtml);

    var $bar = $('.exely-mobile-sticky-bar');
    var $firstRoom = $('.room_cont').first();

    $(window).on('scroll resize', function() {
      if ($(window).width() < 768) {
        var scrollTop = $(window).scrollTop();
        if ($firstRoom.length && scrollTop > 250) {
          $bar.addClass('visible');
        } else {
          $bar.removeClass('visible');
        }
      } else {
        $bar.removeClass('visible');
      }
    });

    $(document).on('click', '.exely-mobile-sticky-btn', function() {
      if ($('.btn_book_now_with_occupancy:first').length) {
        $('html, body').animate({
          scrollTop: $('.room_cont:first').offset().top - 80
        }, 400);
      }
    });
  }

  /**
   * 3. Nâng cấp trải nghiệm thẻ phòng (Hover & Micro-interactions)
   */
  function enhanceRoomCards() {
    $('.room_cont').each(function() {
      var $card = $(this);
      
      // Thêm hiệu ứng gợn sóng nhẹ khi bấm nút Đặt phòng
      $card.find('.btn_book_now_with_occupancy').on('mousedown', function() {
        $(this).css('transform', 'scale(0.97)');
      }).on('mouseup mouseleave', function() {
        $(this).css('transform', '');
      });
    });
  }

})(jQuery);
