import cv2
import numpy as np


def align_and_crop_id(image_path):
    # Read the image
    img = cv2.imread(image_path)

    # Convert to grayscale
    gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)

    # Apply Gaussian blur to reduce noise
    blurred = cv2.GaussianBlur(gray, (5, 5), 0)

    # Apply threshold
    _, thresh = cv2.threshold(blurred, 0, 255, cv2.THRESH_BINARY + cv2.THRESH_OTSU)

    # Find contours
    contours, _ = cv2.findContours(thresh, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)

    # Find the largest contour (assumed to be the ID card)
    largest_contour = max(contours, key=cv2.contourArea)

    # Get the minimum area rectangle
    rect = cv2.minAreaRect(largest_contour)
    box = cv2.boxPoints(rect)
    box = np.int0(box)

    # Get width and height of the detected rectangle
    width = int(rect[1][0])
    height = int(rect[1][1])

    # Ensure width is greater than height
    if width < height:
        width, height = height, width

    # Get the transformation matrix
    src_pts = box.astype("float32")
    dst_pts = np.array([[0, height - 1],
                        [0, 0],
                        [width - 1, 0],
                        [width - 1, height - 1]], dtype="float32")

    # Apply perspective transform
    M = cv2.getPerspectiveTransform(src_pts, dst_pts)
    warped = cv2.warpPerspective(img, M, (width, height))

    # Save the aligned and cropped image
    output_path = 'aligned_id_card.jpg'
    cv2.imwrite(output_path, warped)
    print(f"Aligned and cropped image saved as: {output_path}")

    return warped


# Function to enhance image quality
def enhance_image(image):
    # Convert to LAB color space
    lab = cv2.cvtColor(image, cv2.COLOR_BGR2LAB)

    # Split channels
    l, a, b = cv2.split(lab)

    # Apply CLAHE to L channel
    clahe = cv2.createCLAHE(clipLimit=3.0, tileGridSize=(8, 8))
    cl = clahe.apply(l)

    # Merge channels
    merged = cv2.merge([cl, a, b])

    # Convert back to BGR
    enhanced = cv2.cvtColor(merged, cv2.COLOR_LAB2BGR)

    return enhanced


def process_id_card(input_path):
    # Align and crop
    aligned = align_and_crop_id(input_path)

    # Enhance image quality
    enhanced = enhance_image(aligned)

    # Save final result
    output_path = 'final_processed_id.jpg'
    cv2.imwrite(output_path, enhanced)
    print(f"Final processed image saved as: {output_path}")


# Use the function
input_image_path = 'Screenshot 2025-01-17 130117.png'  # Replace with your image path
process_id_card(input_image_path)