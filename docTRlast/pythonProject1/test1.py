import os
from glob import glob
from doctr.io import DocumentFile
from doctr.models import ocr_predictor
import re

# Define the root directory containing the nested folders
root_dir = "images"

# Load the OCR model from docTR
model = ocr_predictor(pretrained=True)

# Keywords to search for
keywords = ["Prénom", "Nom", "Le candidat(e)"]


# Function to extract names using keywords
def extract_names(text, keywords):
    name_info = {}
    lines = text.split("\n")  # Split text into lines

    for line in lines:
        for keyword in keywords:
            if keyword in line:
                # Extract the value after the keyword
                value = line.split(keyword)[-1].strip()
                name_info[keyword] = value
                break  # Stop searching for other keywords in this line

    return name_info


# Function to extract capitalized words
def extract_capital_words(result):
    capital_words = []
    for page in result.pages:
        for block in page.blocks:
            for line in block.lines:
                for word in line.words:
                    word_text = word.value
                    # Check if the word is in capital letters and length > 2
                    if word_text.isupper() and len(word_text) > 2:
                        capital_words.append(word_text)
    return capital_words


# Function to process each image
def process_image(image_path):
    # Load the image using docTR
    doc = DocumentFile.from_images(image_path)

    # Perform OCR
    result = model(doc)

    # Extract the text from the OCR result
    extracted_text = result.render()

    # First attempt: Extract names using keywords
    name_info = extract_names(extracted_text, keywords)

    if name_info:
        # If keywords are found, print the extracted names
        print(f"Extracted names from {image_path}:")
        for key, value in name_info.items():
            print(f"{key}: {value}")
    else:
        # Fallback: Extract capitalized words
        capital_words = extract_capital_words(result)

        # Check if there are at least 7 capitalized words
        if len(capital_words) >= 7:
            sixth_word = capital_words[5]
            seventh_word = capital_words[6]
            print(f"Fallback extraction from {image_path}:")
            print(f"Prenom: {sixth_word}")
            print(f"Nom: {seventh_word}")
        else:
            print(f"No keywords or sufficient capitalized words found in {image_path}")


# Traverse through the nested directories
for subdir, _, _ in os.walk(root_dir):
    # Find all image files in the current directory
    for image_path in glob(os.path.join(subdir, "*.png")):
        process_image(image_path)